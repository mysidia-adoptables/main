<?php

/**
 * Represents a document type, contains information on which modules
 * need to be loaded.
 * @note This class is inspected by Printer_HTMLDefinition->renderDoctype.
 *       If structure changes, please update that function.
 */
class HTMLPurifier_Doctype
{
    public function __construct(
        /**
         * Full name of doctype
         * @type string
         */
        public $name = null,
        /**
         * Is the language derived from XML (i.e. XHTML)?
         * @type bool
         */
        public $xml = true,
        /**
         * List of standard modules (string identifiers or literal objects)
         * that this doctype uses
         * @type array
         */
        public $modules = [],
        /**
         * List of modules to use for tidying up code
         * @type array
         */
        public $tidyModules = [],
        /**
         * List of aliases for this doctype
         * @type array
         */
        public $aliases = [],
        /**
         * Public DTD identifier
         * @type string
         */
        public $dtdPublic = null,
        /**
         * System DTD identifier
         * @type string
         */
        public $dtdSystem = null
    )
    {
    }
}

// vim: et sw=4 sts=4
