/* YUI 3.9.0 (build 5827) Copyright 2013 Yahoo! Inc. http://yuilibrary.com/license/ */
YUI.add("autocomplete-plugin",function(e,t){function r(e){e.inputNode=e.host,!e.render&&e.render!==!1&&(e.render=!0),r.superclass.constructor.apply(this,arguments)}var n=e.Plugin;e.extend(r,e.AutoCompleteList,{},{NAME:"autocompleteListPlugin",NS:"ac",CSS_PREFIX:e.ClassNameManager.getClassName("aclist")}),n.AutoComplete=r,n.AutoCompleteList=r},"3.9.0",{requires:["autocomplete-list","node-pluginhost"]});