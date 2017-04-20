
# Joomla Manifest to MarkDown by AllEvents![](http//marknotes.allevents3.com/docs/.images/allevents-hauteur.png)

![](https://img.shields.io/badge/AllEvents-v3.4-blue.svg)   ![](https://img.shields.io/badge/licence-GNU--GPL-green.svg)<br/><br/>

This page to initiate a program to export Joomla's manifest documentation into Markdown format.

o use it : 
```
require_once(JPATH_SITE . '/administrator/components/com_allevents/helpers/MakeMD.php');
$g_se_MD = new AllEventsClassMD();
$msg = $g_se_MD->MakeMDExtension();
$msg .= '<br/>, ' . $g_se_MD->MakeMDConfig();
$msg .= '<br/>, ' . $g_se_MD->MakeMDObject("com_allevents", "activity");
...
$msg .= '<br/>, ' . $g_se_MD->MakeMD('mod_aebanner', 'modules');
...
$msg .= '<br/>, ' . $g_se_MD->MakeMD('allevents', 'plugins', 'acymailing');
...
$msg .= '<br/>, ' . $g_se_MD->MakeMD('default', 'views', 'agendas');
...
```

For this time (20/04/2017) the program is (very) oriented to generate [AllEvents](https://www.allevents3.com) documentation.

Any idea ? contact me :)
