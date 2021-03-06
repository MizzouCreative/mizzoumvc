<?php
namespace MizzouMVC\library;
use MizzouMVC\library\ViewEngineLoader as ViewEngineLoader;
if (defined('WP_CLI') && WP_CLI) {
    require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'ViewEngineLoader.php';

    /**
     * MizzouMVC related WPCLI commands
     * Class MizzouMvcCLI
     * @package MizzouMVC\library
     */
    class MizzouMvcCLI
    {

        /**
         * Clears the Twig cache
         * @subcommand clear-cache
         * @alias cc
         * @return void
         */
        public function clearcache()
        {
            if (!defined('MIZZOUMVC_VERSION')) {
                \WP_CLI::error('This site does not have the MizzouMVC plugin installed and activated');
                return;
            }

            $objViewEngine = ViewEngineLoader::getViewEngine();

            $strCacheLocation = $objViewEngine->getCache();

            if (!file_exists($strCacheLocation)) {
                \WP_CLI::error('The location ' . $strCacheLocation . ' does not exist.');
                return;
            }

            $objDirectory = new \FilesystemIterator($strCacheLocation);
            if (!$objDirectory->valid()) {
                \WP_CLI::error('Looks like ' . $strCacheLocation . ' is already emtpy!');
                return;
            }

            \WP_CLI::log('Deleting cache from ' . $strCacheLocation . '...');
            $objViewEngine->clearCacheFiles();
            \WP_CLI::success('Cache deleted');
        }
    }

    \WP_CLI::add_command('mizzoumvc', 'MizzouMVC\library\MizzouMvcCLI');
}
