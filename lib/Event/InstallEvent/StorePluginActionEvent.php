<?php

/**
 * User: joachimdoerr
 * Date: 01.08.17
 * Time: 23:14
 */
class StorePluginActionEvent extends GenericEvent
{
    /**
     * StorePluginInstallEvent constructor.
     * @param rex_plugin $plugin
     * @param rex_addon $addon
     * @author Joachim Doerr
     */
    public function __construct(rex_plugin $plugin, rex_addon $addon)
    {
        parent::__construct($plugin->getName(), array('plugin'=>$plugin, 'addon'=>$addon));
    }

    /**
     * @return rex_plugin
     * @author Joachim Doerr
     */
    public function getPlugin()
    {
        return $this->getArgument('plugin');
    }

    /**
     * @return rex_addon
     * @author Joachim Doerr
     */
    public function getAddon()
    {
        return $this->getArgument('addon');
    }
}