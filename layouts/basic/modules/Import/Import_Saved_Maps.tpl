{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}

<select name="saved_maps" id="saved_maps" class="font-x-small select2" onchange="ImportJs.loadSavedMap();"
		data-placeholder="--{\App\Language::translate('LBL_SELECT_SAVED_MAPPING', $MODULE)}--"
		data-select="allowClear, true">
	<optgroup class="p-0">
		<option id="-1" value="" selected>--{\App\Language::translate('LBL_SELECT_SAVED_MAPPING', $MODULE)}--</option>
	</optgroup>
	{foreach key=_MAP_ID item=_MAP from=$SAVED_MAPS}
		<option id="{$_MAP_ID}" value="{$_MAP->getStringifiedContent()}">{$_MAP->getValue('name')}</option>
	{/foreach}
</select>
<span id="delete_map_container" style="display:none;">
	<i class="fas fa-trash-alt u-cursor-pointer" onclick="ImportJs.deleteMap('{$FOR_MODULE}');"
	   alt="{\App\Language::translate('LBL_DELETE', $FOR_MODULE)}"></i>
</span>
