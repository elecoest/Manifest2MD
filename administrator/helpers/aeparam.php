<?php

defined('_JEXEC') or die;

/**
 * AllEventsHelperParam
 *
 * @version %%ae3.version%%
 * @package %%ae3.package%%
 * @copyright %%ae3.copyright%%
 * @license %%ae3.license%%
 * @author %%ae3.author%%
 * @access public
 */
class AllEventsHelperParam
{
    /**
     * AllEventsHelperParam::getGlobalParam()
     *
     * @return \Joomla\Registry\Registry
     * @throws Exception
     */
    public static function getGlobalParam()
    {
        $app = JFactory::getApplication();
        if ($app->isSite()) {
            $app = JFactory::getApplication();
            $params = $app->getParams('com_manifest2md');
        } else {
            $params = JComponentHelper::getParams('com_manifest2md');
        }

        $params['doc_home'] = isset($params['doc_home']) ? $params['doc_home'] : "";
        $params['template_config'] = isset($params['template_config']) ? $params['template_config'] : "";
        $params['template_item'] = isset($params['template_item']) ? $params['template_item'] : "";
        $params['template_module'] = isset($params['template_module']) ? $params['template_module'] : "";
        $params['template_plugin'] = isset($params['template_plugin']) ? $params['template_plugin'] : "";
        $params['template_view'] = isset($params['template_view']) ? $params['template_view'] : "";

        return $params;
    }
}
