/**
     * AllEventsController::MakeMD()
     *
     */
    function MakeMD()
    {
        require_once(JPATH_SITE . '/administrator/components/com_flexicontent/helpers/MakeMD.php');
        $g_se_MD = new AllEventsClassMD();
        $msg = $g_se_MD->MakeMDExtension();
        $msg .= '<br/>, ' . $g_se_MD->MakeMDConfig();

        $msg .= '<br/>, ' . $g_se_MD->MakeMDObject('com_flexicontent', 'author');
        $msg .= '<br/>, ' . $g_se_MD->MakeMDObject('com_flexicontent', 'category');
        $msg .= '<br/>, ' . $g_se_MD->MakeMDObject('com_flexicontent', 'field');
        $msg .= '<br/>, ' . $g_se_MD->MakeMDObject('com_flexicontent', 'group');
        $msg .= '<br/>, ' . $g_se_MD->MakeMDObject('com_flexicontent', 'item');
        $msg .= '<br/>, ' . $g_se_MD->MakeMDObject('com_flexicontent', 'type');
        $msg .= '<br/>, ' . $g_se_MD->MakeMDObject('com_flexicontent', 'user');

        $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'category');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'favourites');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'filemanager');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'fileselement');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'flexicontent');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'item');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'itemelement');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'search');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'tags');
        
		    $msg .= '<br/>, ' . $g_se_MD->MakeMD('mod_flexiadvsearch', 'modules');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('mod_flexicategories', 'modules');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('mod_flexicontent', 'modules');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('mod_flexifilter', 'modules');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('mod_flexigooglemap', 'modules');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('mod_flexitagcloud', 'modules');
        
		    $msg .= '<br/>, ' . $g_se_MD->MakeMD('flexibreak', 'plugins', 'content');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('flexicontent', 'plugins', 'finder');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('flexinotify', 'plugins', 'flexicontent');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('flexiadvsearch', 'plugins', 'search');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('flexisearch', 'plugins', 'search');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('flexiadvroute', 'plugins', 'system');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('flexisystem', 'plugins', 'system');
        $msg .= '<br/>, ' . $g_se_MD->MakeMD('account_via_submit', 'plugins', 'flexicontent_fields');

        $this->setRedirect('index.php?option=com_flexicontent', $msg);
    }
