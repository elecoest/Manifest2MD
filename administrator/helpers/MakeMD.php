<?php

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * AllEventsClassMD
 *
 * @version %%ae3.version%%
 * @package %%ae3.package%%
 * @copyright %%ae3.copyright%%
 * @license %%ae3.license%%
 * @author %%ae3.author%%
 * @access public
 */
class AllEventsClassMD
{
    protected $root = null;
    protected $params = [];
    protected $language = 'en-GB';

    /**
     * AllEventsClassMD::MakeMDViews(
     *
     * @param string $extension
     * @param string $category
     * @param string $identifier
     * @return string
     */
    public function MakeMDViews($category = "AllEvents", $extension = "com_allevents", $identifier = "site")
    {
        $msg = "";
        //$list = array();

        //get the list of all .xml files in the folder
        if ($identifier == "site") {
            $base_dir = JPATH_ROOT . '/components/' . $extension . '/views/';
        } else {
            $base_dir = JPATH_ROOT . '/administrator/components/' . $extension . '/views/';
        }

        foreach (scandir($base_dir) as $file) {
            if ($file == '.' || $file == '..') continue;
            $dir = $base_dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($dir)) {
                $msg .= '<br/>, ' . self::MakeMDView($category, $extension, $file, $identifier);
            }
        }

        return $msg;
    }

    /**
     * AllEventsClassMD::MakeMD()
     *
     * @param $extension
     * @param string $subpath
     * @param string $category
     * @return int
     * @internal param mixed $entity
     * @internal param mixed $entities
     */
    public function MakeMDView($category = "AllEvents", $extension, $subpath = "", $identifier="site")
    {
        $lang = JFactory::getLanguage();
        $lang->load($extension, JPATH_ADMINISTRATOR, $this->language, true);
        $lang->load($extension, JPATH_SITE, $this->language, true);

        $db = JFactory::getDbo();
        $extension_date = "";
        $extension_author = "";
        $extension_authorEmail = "";
        $get_xml = null;
        $home = null;
        $query = $db->getQuery(true);

        $query->select($db->quoteName(['name', 'manifest_cache']))
            ->from($db->quoteName('#__extensions'))
            ->where('element = ' . $db->quote($extension))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'));

        $db->setQuery($query);

        $results = $db->loadObjectList();

        foreach ($results as $result) {
            $decode = json_decode($result->manifest_cache);
            $extension_date = $decode->creationDate;
            $extension_author = $decode->author;
            $extension_authorEmail = $decode->authorEmail;
        }

        $get_xml = simplexml_load_file(JPATH_ROOT . '/components/' . $extension . '/views/' . $subpath . '/tmpl/default.xml');
        $extension_name = $get_xml->layout['title'];
        if (empty($extension_name)) {
            $extension_name = 'views_' . $subpath;
        }
        $extension_name = JText::_($extension_name);
        $filename = $this->root . $category . '/views/' . $identifier . '/' . $extension_name . '.md';

        if (!empty($get_xml->creationDate)) {
            $extension_date = $get_xml->creationDate;
        }
        if (!empty($get_xml->author)) {
            $extension_author = $get_xml->author;
        }
        if (!empty($get_xml->authorEmail)) {
            $extension_authorEmail = $get_xml->authorEmail;
        }

        if (!empty($filename)) {
            $handle = fopen($filename, 'w');

            $description = trim($get_xml->layout->message);
            $healthy = ["<![CDATA[", "]]>"];
            $yummy = ["", ""];
            $description = str_replace($healthy, $yummy, $description);
            $description = JText::_($description);

            //parameters
            $parameters = "";
            foreach ($get_xml->fields as $fieldset) {				
			    if (($fieldset['name'] == 'request') || ($fieldset['name'] == 'params')) {
                    $parameters .= '### ' . JText::_($fieldset['name']) . PHP_EOL;
                    $parameters .= '| Option | Description | Value |' . PHP_EOL;
                    $parameters .= '| ------ | ----------- | ----- |' . PHP_EOL;

                    foreach ($fieldset->fieldset->field as $field) {
                        $first = true;
                        $str = "";
                        foreach ($field->option as $option) {
                            if ($first) {
                                $str .= '`' . JText::_($option) . '`';
                                $first = false;
                            } else {
                                $str .= ', `' . JText::_($option) . '`';
                            }
                        }
                        // $sLine = '| ' . JText::_($field['label']) . ' | ' . JText::_($field['description']) . ' | ' . $str . '|';
                        if (!empty($field['default'])) {
                            $sLine = '| &nbsp;' . JText::_(empty($field['label']) ? $field['name'] : $field['label']) . ' | ' . JText::_($field['description']) . ' | ' . (($field['type'] != "hidden") ? $str : '') . (empty($field['default']) ? '' : '(default:`' . JText::_($field['default']) . '`)') . '|';
                        } else {
                            $sLine = '| &nbsp;' . JText::_(empty($field['label']) ? $field['name'] : $field['label']) . ' | ' . JText::_($field['description']) . ' | ' . $str . '|';
                        }

                        $parameters .= $sLine . PHP_EOL;
                    }
                }
            }

            $content = $this->params['template_view'];

            // merge
            $content = str_replace('{category}', $category, $content);
            $content = str_replace('{extension}', $extension, $content);
            $content = str_replace('{extension_name}', $extension_name, $content);
            $content = str_replace('{extension_date}', $extension_date, $content);
            $content = str_replace('{extension_author}', $extension_author, $content);
            $content = str_replace('{extension_authorEmail}', $extension_authorEmail, $content);
            $content = str_replace('{description}', $description, $content);
            $content = str_replace('{parameters}', $parameters, $content);
            $content = str_replace('{language}', $this->language, $content);

            // final writing
            $handle = fopen($filename, 'w');
            fwrite($handle, $content);
            fclose($handle);

        }
        return JPATH_ROOT . '/components/' . $extension . '/views/' . $subpath . '/tmpl/default.xml';
    }

    /**
     * AllEventsClassMD::MakeMDObjects(
     *
     * @param string $extension
     * @param string $category
     * @param string $identifier
     * @return string
     */
    public function MakeMDObjects($category = "AllEvents", $extension = "com_allevents", $identifier = "site")
    {
        $msg = "";
        $list = array();

        //get the list of all .xml files in the folder
        if ($identifier == "site") {
            $original = JFolder::files(JPATH_ROOT . '/components/' . $extension . '/models/forms/', '.xml');
        } elseif ($identifier == "administrator") {
            $original = JFolder::files(JPATH_ROOT . '/administrator/components/' . $extension . '/models/forms/', '.xml');
        }

        //create the final list that contains name of files
        $total = count($original);
        $index = 0;
        for ($i = 0; $i < $total; $i++) {
            //separate name&extension si besoin ...
            //remove the file extension and the dot from the filename
            $list[$index]['name'] = substr($original[$i], 0, -1 * (1 + strlen(JFile::getExt($original[$i]))));
            //add the extension
            // $list[$index]['ext'] = JFile::getExt($original[$i]);
            $msg .= '<br/>, ' . self::MakeMDObject($category, $extension, $list[$index]['name'], $identifier);
            $index++;
        }
        return $msg;
    }

    /**
     * AllEventsClassMD::MakeMDObject()
     *
     * @param string $extension
     * @param string $object
     * @param string $category
     * @param string $identifier
     * @return string
     */
    public function MakeMDObject($category = "AllEvents", $extension = "com_allevents", $object = "event", $identifier = "site")
    {
        $lang = JFactory::getLanguage();
        $lang->load($extension, JPATH_ADMINISTRATOR, $this->language, true);
        $lang->load($extension . '.sys', JPATH_ADMINISTRATOR, $this->language, true);

        if ($identifier == "site") {
            $get_xml = simplexml_load_file(JPATH_ROOT . '/components/' . $extension . '/models/forms/' . $object . '.xml');
        } elseif ($identifier == "administrator") {
            $get_xml = simplexml_load_file(JPATH_ROOT . '/administrator/components/' . $extension . '/models/forms/' . $object . '.xml');
        }
        $filename = $this->root . $category . '/items/' . $identifier . '/' . $object . '.md';

        //parameters
        $parameters = "";
		$home = null ; 
		$home = (empty($get_xml->fieldset)) ? $get_xml  : $get_xml->fieldset ; 
        foreach ($home as $fieldset) {
            $parameters .= '### ' . JText::_($fieldset['name']) . PHP_EOL;
            $parameters .= '| Option | Description | Type | Value |' . PHP_EOL;
            $parameters .= '| ------ | ----------- | ---- | ----- |' . PHP_EOL;

            foreach ($fieldset->field as $field) {
                $first = true;
                $str = "";
                foreach ($field->option as $option) {
                    if ($first) {
                        $str .= '`' . JText::_($option) . '`';
                        $first = false;
                    } else {
                        $str .= ', `' . JText::_($option) . '`';
                    }
                }
                $default = (isset($field['default'])) ? ' (default: `' . JText::_($field['default']) . '`)' : '';
                $sLine = '| &nbsp;' . (empty(JText::_($field['label'])) ? JText::_($field['name']) : JText::_($field['label'])) . ' | ' . JText::_($field['description']) . ' | ' . JText::_($field['type']) . ' | ' . $str . $default . '|';
                $parameters .= $sLine . PHP_EOL;
            }
        }

        $content = $this->params['template_item'];

        // merge
        $content = str_replace('{category}', $category, $content);
        $content = str_replace('{object}', $object, $content);
        $content = str_replace('{parameters}', $parameters, $content);
        $content = str_replace('{language}', $this->language, $content);

        // final writing
        $handle = fopen($filename, 'w');
        fwrite($handle, $content);
        fclose($handle);

        return (JPATH_ROOT . '/administrator/components/' . $extension . '/models/forms/' . $object . '.xml');
    }

    /**
     * AllEventsClassMD::MakeMDComponent(
     *
     * @param string $extension
     * @param string $category
     * @param string $identifier
     * @return string
     */
    public function MakeMDComponent($category = "AllEvents", $extension = "com_allevents", $identifier = "site")
    {
        $msg = "";
        $list = array();

        //get the list of all .xml files in the folder
        $original = JFolder::files(JPATH_ROOT . '/administrator/components/' . $extension . '/', '.xml');

        //create the final list that contains name of files
        $total = count($original);
        $index = 0;
        for ($i = 0; $i < $total; $i++) {
            //separate name&extension si besoin ...
            //remove the file extension and the dot from the filename
            $list[$index]['name'] = substr($original[$i], 0, -1 * (1 + strlen(JFile::getExt($original[$i]))));
            //add the extension
            // $list[$index]['ext'] = JFile::getExt($original[$i]);
            $msg .= '<br/>, ' . self::MakeMDExtension($category, $extension, $list[$index]['name']);
            $index++;
        }
        return $msg;
    }

    /**
     * AllEventsClassMD::MakeMDExtension()
     *
     * @param string $extension
     * @param string $category
     * @param string $name
     * @return int
     * @internal param mixed $entity
     * @internal param mixed $entities
     */
    public function MakeMDExtension($category = "AllEvents", $extension = "com_allevents", $name = 'allevents')
    {
        $get_xml = simplexml_load_file(JPATH_ROOT . '/administrator/components/' . $extension . '/' . $name . '.xml');
        $filename = $this->root . $category . '/' . $extension . '.md';
        $handle = fopen($filename, 'w');

        fwrite($handle, '# ' . $extension . ' Component' . PHP_EOL);

        fwrite($handle, '## Modules' . PHP_EOL);
        foreach ($get_xml->modules->module as $module) {
            fwrite($handle, '### ' . $module['name'] . PHP_EOL);
        }

        fwrite($handle, '## Plugins' . PHP_EOL);
        foreach ($get_xml->plugins->plugin as $plugin) {
            fwrite($handle, '### ' . $plugin['name'] . PHP_EOL);
        }
        fclose($handle);
        return (JPATH_ROOT . '/administrator/components/' . $extension . '/' . $name . '.xml');
    }

    /**
     * AllEventsClassMD::MakeMD()
     *
     * @param string $extension
     * @param string $category
     * @return int
     * @internal param string $subpath
     * @internal param mixed $entity
     * @internal param mixed $entities
     */
    public function MakeMDModule($category = "AllEvents", $extension = "mod_aesearch")
    {
        $lang = JFactory::getLanguage();
        $lang->load($extension, JPATH_ADMINISTRATOR, $this->language, true);
        $lang->load($extension, JPATH_SITE, $this->language, true);

        $db = JFactory::getDbo();
        $extension_date = "";
        $extension_author = "";
        $extension_authorEmail = "";
        $query = $db->getQuery(true);

        $query->select($db->quoteName(['name', 'manifest_cache']))
            ->from($db->quoteName('#__extensions'))
            ->where('element = ' . $db->quote($extension))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'));

        $db->setQuery($query);

        $results = $db->loadObjectList();

        foreach ($results as $result) {
            $decode = json_decode($result->manifest_cache);
            $extension_date = $decode->creationDate;
            $extension_author = $decode->author;
            $extension_authorEmail = $decode->authorEmail;
        }

        $get_xml = simplexml_load_file(JPATH_ROOT . '/modules/' . $extension . '/' . $extension . '.xml');
        $extension_name = empty($get_xml->name) ? $extension : $get_xml->name;
        $extension_name = JText::_($extension_name);

        $filename = $this->root . $category . '/modules/' . $extension_name . '.md';

        if (!empty($get_xml->creationDate)) {
            $extension_date = $get_xml->creationDate;
        }
        if (!empty($get_xml->author)) {
            $extension_author = $get_xml->author;
        }
        if (!empty($get_xml->authorEmail)) {
            $extension_authorEmail = $get_xml->authorEmail;
        }

        // description
        $description = trim($get_xml->description);
        $healthy = ["<![CDATA[", "]]>"];
        $yummy = ["", ""];
        $description = str_replace($healthy, $yummy, $description);
        $description = JText::_($description);

        // parameters
        $parameters = '';

        foreach ($get_xml->config->fields->fieldset as $fieldset) {
            $parameters .= '### ' . JText::_($fieldset['name']) . PHP_EOL;
            $parameters .= '| Option | Description | Value |' . PHP_EOL;
            $parameters .= '| ------ | ----------- | ----- |' . PHP_EOL;

            foreach ($fieldset->field as $field) {
                $first = true;
                $str = "";
                foreach ($field->option as $option) {
                    if ($first) {
                        $str .= '`' . JText::_($option) . '`';
                        $first = false;
                    } else {
                        $str .= ', `' . JText::_($option) . '`';
                    }
                }
                $str = ($field['type'] != 'hidden') ? $str : '';
                $default = (!empty($field['default'])) ? '(default:`' . JText::_($field['default']) . '`)' : '';
                $sLine = '| &nbsp;' . JText::_(empty($field['label']) ? $field['name'] : $field['label']) . ' | ' . JText::_($field['description']) . ' | ' . $str . $default . '|';

                $parameters .= $sLine . PHP_EOL;
            }
        }

        $content = $this->params['template_module'];

        // merge
        $content = str_replace('{category}', $category, $content);
        $content = str_replace('{extension}', $extension, $content);
        $content = str_replace('{extension_name}', $extension_name, $content);
        $content = str_replace('{extension_date}', $extension_date, $content);
        $content = str_replace('{extension_author}', $extension_author, $content);
        $content = str_replace('{extension_authorEmail}', $extension_authorEmail, $content);
        $content = str_replace('{description}', $description, $content);
        $content = str_replace('{parameters}', $parameters, $content);
        $content = str_replace('{language}', $this->language, $content);

        // final writing
        $handle = fopen($filename, 'w');
        fwrite($handle, $content);
        fclose($handle);

        return $filename;
    }

    /**
     * AllEventsClassMD::MakeMDPlugin()
     *
     * @param string $extension
     * @param string $subpath
     * @param string $category
     * @return int
     * @internal param mixed $entity
     * @internal param mixed $entities
     */
    public function MakeMDPlugin($category = "AllEvents", $extension = "", $subpath = "")
    {
        $lang = JFactory::getLanguage();
        // $lang->load($extension, JPATH_ADMINISTRATOR, $this->language, true);
        // $lang->load($extension, JPATH_SITE, $this->language, true);
        $lang->load('plg_' . $subpath . '_' . $extension, JPATH_SITE, $this->language, true);
        $lang->load('plg_' . $subpath . '_' . $extension, JPATH_ADMINISTRATOR, $this->language, true);

        $db = JFactory::getDbo();
        $extension_date = "";
        $extension_author = "";
        $extension_authorEmail = "";
        $get_xml = null;
        $home = null;
        $query = $db->getQuery(true);

        $query->select($db->quoteName(['name', 'manifest_cache']))
            ->from($db->quoteName('#__extensions'))
            ->where('element = ' . $db->quote($extension))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'));

        $db->setQuery($query);

        $results = $db->loadObjectList();

        foreach ($results as $result) {
            $decode = json_decode($result->manifest_cache);
            $extension_date = $decode->creationDate;
            $extension_author = $decode->author;
            $extension_authorEmail = $decode->authorEmail;
        }

        $get_xml = simplexml_load_file(JPATH_ROOT . '/plugins/' . $subpath . '/' . $extension . '/' . $extension . '.xml');

        $extension_name = $get_xml->name;
        if (empty($extension_name)) {
            $extension_name = $extension;
        }
        $extension_name = JText::_($extension_name);

        $filename = $this->root . $category . '/plugins/' . $subpath . '_' . $extension_name . '.md';

        if (!empty($get_xml->creationDate)) {
            $extension_date = JText::_($get_xml->creationDate);
        }
        if (!empty($get_xml->author)) {
            $extension_author = JText::_($get_xml->author);
        }
        if (!empty($get_xml->authorEmail)) {
            $extension_authorEmail = JText::_($get_xml->authorEmail);
        }

        // description
        $description = trim($get_xml->description);
        $healthy = ["<![CDATA[", "]]>"];
        $yummy = ["", ""];
        $description = str_replace($healthy, $yummy, $description);
        $description = JText::_($description);

        // parameters
        $parameters = "";
        foreach ($get_xml->config->fields->fieldset as $fieldset) {
            $parameters .= '### ' . JText::_($fieldset['name']) . PHP_EOL;
            $parameters .= '| Option | Description | Value |' . PHP_EOL;
            $parameters .= '| ------ | ----------- | ----- |' . PHP_EOL;

            foreach ($fieldset->field as $field) {
                $first = true;
                $str = "";
                foreach ($field->option as $option) {
                    if ($first) {
                        $str .= '`' . JText::_($option) . '`';
                        $first = false;
                    } else {
                        $str .= ', `' . JText::_($option) . '`';
                    }
                }
                $str = ($field['type'] != 'hidden') ? $str : '';
                $default = (!empty($field['default'])) ? '(default:`' . JText::_($field['default']) . '`)' : '';
                $sLine = '| &nbsp;' . JText::_(empty($field['label']) ? $field['name'] : $field['label']) . ' | ' . JText::_($field['description']) . ' | ' . $str . $default . '|';

                $parameters .= $sLine . PHP_EOL;
            }
        }

        $content = $this->params['template_plugin'];

        // merge
        $content = str_replace('{category}', $category, $content);
        $content = str_replace('{extension}', $extension, $content);
        $content = str_replace('{extension_name}', $extension_name, $content);
        $content = str_replace('{extension_date}', $extension_date, $content);
        $content = str_replace('{extension_author}', $extension_author, $content);
        $content = str_replace('{extension_authorEmail}', $extension_authorEmail, $content);
        $content = str_replace('{description}', $description, $content);
        $content = str_replace('{parameters}', $parameters, $content);
        $content = str_replace('{language}', $this->language, $content);

        // final writing
        $handle = fopen($filename, 'w');
        fwrite($handle, $content);
        fclose($handle);

        return $filename;
    }

    /**
     * AllEventsClassMD::MakeMDConfig()
     *
     * @param string $extension
     * @param string $category
     * @return string
     */
    public function MakeMDConfig($category = "AllEvents", $extension = "com_allevents")
    {
        $lang = JFactory::getLanguage();
        $lang->load($extension, JPATH_ADMINISTRATOR, $this->language, true);
        $lang->load($extension . '.sys', JPATH_ADMINISTRATOR, $this->language, true);

        $get_xml = simplexml_load_file(JPATH_ROOT . '/administrator/components/' . $extension . '/config.xml');
        $filename = $this->root . $category . '/config_' . $extension . '.md';

		$parameters = "" ; 
		foreach ($get_xml->fieldset as $fieldset) {			
			// parameters
			$parameters .= '### ' . JText::_($fieldset['name']) . PHP_EOL;
			$parameters .= '| Option | Description | Value |' . PHP_EOL;
			$parameters .= '| ------ | ----------- | ----- |' . PHP_EOL;
	
			foreach ($fieldset->field as $field) {
				if ($field['type'] == 'rules') {
					$get_rules = simplexml_load_file(JPATH_ROOT . '/administrator/components/' . $extension . '/access.xml');
					$parameters .= '## ' . JText::_($field['label']) . ' ...' . PHP_EOL;
					$parameters .= '| Action | Description |' . PHP_EOL;
					$parameters .= '| ------ | ----------- |' . PHP_EOL;
					foreach ($get_rules->section->action as $action) {
						$sLine = ' | ' . JText::_($action['title']) . ' | ' . JText::_($action['description']) . ' | ';
						$parameters .= $sLine . PHP_EOL;
					}
				} else {
					$first = true;
					$str = "";
					foreach ($field->option as $option) {
						if ($first) {
							$str .= '`' . JText::_($option) . '`';
							$first = false;
						} else {
							$str .= ', `' . JText::_($option) . '`';
						}
					}
					$default = (isset($field['default'])) ? ' (default: `' . JText::_($field['default']) . '`)' : '';
					$sLine = '| &nbsp;' . JText::_($field['label']) . ' | ' . JText::_($field['description']) . ' | ' . $str . $default . '|';
					$parameters .= $sLine . PHP_EOL;
				}
			}
		}
        $content = $this->params['template_config'];

        // merge
        $content = str_replace('{category}', $category, $content);
        $content = str_replace('{extension}', $extension, $content);
        $content = str_replace('{parameters}', $parameters, $content);
        $content = str_replace('{language}', $this->language, $content);

        // final writing
        $handle = fopen($filename, 'w');
        fwrite($handle, $content);
        fclose($handle);
        return (JPATH_ROOT . '/administrator/components/' . $extension . '/config.xml');
    }

    /**
     * @param string $category
     */
    public function CheckFolder($category = "AllEvents")
    {
        $folder[0][0] = 'root';
        $folder[0][1] = $this->root . $category;
        $folder[1][0] = 'items';
        $folder[1][1] = $this->root . $category . '/items/';
        $folder[2][0] = 'views';
        $folder[2][1] = $this->root . $category . '/views/';
        $folder[3][0] = 'plugins';
        $folder[3][1] = $this->root . $category . '/plugins/';
        $folder[4][0] = 'modules';
        $folder[4][1] = $this->root . $category . '/modules/';
        $folder[5][0] = 'items/site';
        $folder[5][1] = $this->root . $category . '/items/site';
        $folder[6][0] = 'items/administrator';
        $folder[6][1] = $this->root . $category . '/items/administrator';
        $folder[7][0] = 'views/site';
        $folder[7][1] = $this->root . $category . '/views/site';
        $folder[8][0] = 'views/administrator';
        $folder[8][1] = $this->root . $category . '/views/administrator';
        foreach ($folder as $key => $value) {
            if (!JFolder::exists($value[1])) {
                if (JFolder::create($value[1], 0755)) {
                    //
                } else {
                    //
                }
            } else // Folder exist
            {
                //
            }
        }
    }
	
    /**
     * @param string $lang
     */
    function setLanguage($lang = 'en-GB')
    {
        $this->language = (empty($lang)) ? 'en-GB' : $lang;
    }

    /**
     * @param string $url
     */
    function setRoot($url = JPATH_ROOT . '/documentation/docs/')
    {
        $this->root = $url;
        $this->root = rtrim($this->root, '/') . '/' . $this->language . '/';
    }

    /**
     * @param array $url
     */
    function setParams($params = [])
    {
        $this->params = $params;
    }
}

