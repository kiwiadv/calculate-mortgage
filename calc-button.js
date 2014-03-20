// JavaScript Document

// Script che aggiunge il bottone all'editor
 
// add button to editor

(function() {
 
//Modifica nella riga qui sotto il nome del nostro shortcode con il nome del tuo shortcode
 
tinymce.create('tinymce.plugins.calculate', {
 
init : function(ed, url) {
 
//Modifica nella riga qui sotto il nome del nostro shortcode con il nome del tuo shortcode
 
ed.addButton('calculate', {
 
title : 'Calculate Mortgage',
 
image : url+'/img/money-calc.png',
 
onclick : function() {
 
//Questa riga qui sotto è importantissima: viene aggiunto il tag di apertura e chiusura dello shortcode
 
ed.selection.setContent('[' + ('calculate') + ']');
 
}
 
});
 
},
 
createControl : function(n, cm) {
 
return null;
 
},
 
});
 
//Modifica nella riga qui sotto il nome del nostro shortcode con il nome del tuo shortcode
 
tinymce.PluginManager.add('calculate', tinymce.plugins.calculate);
 
})();