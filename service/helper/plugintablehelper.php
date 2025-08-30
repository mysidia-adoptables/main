<?php

namespace Service\Helper;

use Model\DomainModel\ACPHook;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;
use Resource\GUI\Document\Comment;
use Resource\Utility\URL;

/**
 * The PluginTableHelper Class, extends from the TableHelper class.
 * It is a specific helper for tables that involves operations for plugins in ACP.
 * @category Service
 * @package Helper
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.6
 * @todo Not much at this point.
 *
 */

class PluginTableHelper extends TableHelper
{
    /**
     * The getActionImage method, wraps up the table cell with a plugin action image/link.
     * @param ACPHook  $plugin
     * @access public
     * @return Comment|Link
     */
    public function getActionImage(ACPHook $plugin)
    {
        if (!$plugin) {
            return new Comment("N/A", false);
        }
        $action = $plugin->isEnabled() ? "disable" : "enable";
        $url = new URL("admincp/settings/plugin/{$plugin->getID()}/{$action}");
        $image = new Image(($action == "enable") ? "templates/icons/unfreeze.gif" : "templates/icons/freeze.gif");
        return new Link($url, $image);
    }
}
