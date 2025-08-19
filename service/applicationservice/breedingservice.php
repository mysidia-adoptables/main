<?php

namespace Service\ApplicationService;

use ArrayObject;
use Model\DomainModel\Adoptable;
use Model\DomainModel\BreedAdoptable;
use Model\DomainModel\OwnedAdoptable;
use Model\Settings\BreedingSettings;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Native\MysObject;
use Resource\Utility\Probability;
use Service\Validator\BreedingValidator;

final class BreedingService extends MysObject
{
    private $offsprings;
    private $validator;

    public function __construct(private readonly OwnedAdoptable $female, private readonly OwnedAdoptable $male, private readonly BreedingSettings $settings)
    {
        $this->offsprings = new ArrayObject();
    }

    public function getValidator()
    {
        if (func_num_args() == 0) {
            throw new InvalidActionException("global_action");
        }

        if (func_get_arg(0) == "all") {
            $validations = new ArrayObject(["class", "gender", "owner", "species", "interval", "level", "capacity", "number", "cost", "usergroup", "item", "chance"]);
        } else {
            $validations = new ArrayObject(func_get_args());
        }

        $this->validator = new BreedingValidator($this->female, $this->male, $this->settings, $validations);
        return $this->validator;
    }

    public function getBabySpecies()
    {
        $mysidia = Registry::get("mysidia");
        $female = $this->female->getType();
        $male = $this->male->getType();
        $parentList = "{$female}, {$male}";
        $parentList2 = "{$male}, {$female}";

        $stmt = $mysidia->db->join("adoptables", "adoptables.id = breeding.offspring")
                            ->select("breeding", [], "((mother ='{$female}' and father = '{$male}') or (mother ='{$female}' and father = '') or (mother ='' and father = '{$male}') or parent = '{$female}' or parent = '{$male}' or parent = '{$parentList}' or parent = '{$parentList2}') and level <= {$this->female->getCurrentLevel()} and available = 'yes'");

        if ($stmt->rowCount() == 0) {
            return;
        } else {
            $species = new ArrayObject();
            while ($dto = $stmt->fetchObject()) {
                $adopt = new BreedAdoptable($dto->bid, $dto);
                $species->append($adopt);
            }
            return $species;
        }
    }

    public function getBabyAdopts($species = "")
    {
        if ($this->settings->method == "heuristic" || !$species) {
            $this->heuristicBreed();
        } else {
            $this->advancedBreed($species);
        }
        return $this->offsprings;
    }

    private function heuristicBreed()
    {
        $choices = [$this->female->getType(), $this->male->getType()];
        $num = random_int(1, $this->settings->number);
        for ($i = 0; $i < $num; $i++) {
            $rand = random_int(0, 1);
            $this->offsprings->append(new Adoptable($choices[$rand]));
        }
    }

    private function advancedBreed($species)
    {
        $speciesMap = new ArrayObject();
        $probability = new Probability();

        foreach ($species as $breedAdopt) {
            $speciesMap->offsetSet($breedAdopt->getBreedID(), $breedAdopt);
            $probability->addEvent($breedAdopt->getBreedID(), $breedAdopt->getProbability());
        }

        $num = random_int(1, $this->settings->number);
        for ($i = 0; $i < $num; $i++) {
            $bid = $probability->randomEvent();
            $adopt = $speciesMap->offsetGet($bid);
            if ($this->getSurvival($adopt)) {
                $this->offsprings->append($adopt);
            }
        }
    }

    public function getSurvival(BreedAdoptable $adopt)
    {
        $rand = random_int(0, 99);
        return ($rand < $adopt->getSurvivalRate());
    }

    public function countOffsprings()
    {
        return $this->offsprings->count();
    }

    public function getOffsprings()
    {
        return $this->offsprings;
    }

    public function breed()
    {
        $mysidia = Registry::get("mysidia");
        foreach ($this->offsprings as $adopt) {
            $adopt->makeOwnedAdopt($mysidia->user->getID());
        }
        $this->validator->setStatus("complete");
    }
}
