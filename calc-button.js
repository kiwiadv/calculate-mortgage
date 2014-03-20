// JavaScript Document

// add button to editor

(function() {

tinymce.create('tinymce.plugins.calculate', {

init : function(ed, url) {

ed.addButton('calculate', {

title : 'Calculate Mortgage',

image : url+'/img/money-calc.png',

onclick : function() {

ed.selection.setContent('[' + ('calculate') + ']');

}

});

},

createControl : function(n, cm) {

return null;

},

});

tinymce.PluginManager.add('calculate', tinymce.plugins.calculate);

})();