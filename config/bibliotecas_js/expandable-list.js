/*
 *USAR EM QUALQUER ELEMENTO HTML 
 * DEPENDE APENAS DAS DUAS CLASSES:
 * 		bloco_expansivel: BLOCO CONTEINER DOS BLOCOS INTERNOS
 * 			titulo_expansivel: ELEMENTO QUE SERÁ CLICÁVEL E SEMPRE EXIBIDO
 * 			conteudo_expansivel: BLOCO QUE SERÁ EXIBIDO/OCULTADO
*/
/**************************************************************/
/* Prepares the cv to be dynamically expandable/collapsible   */
/**************************************************************/
function prepareList() {
    $('.titulo_expansivel')
    .on('click', function(event) {
        if (this == event.target) {
			console.log("Processa");
			if($(this).parent().children('.conteudo_expansivel').css('display') == 'none'){
				//esconder todos, mostrar um único por vez
				//$(this).parent().parent().find('.conteudo_expansivel').hide('medium');
				$(this).parent().children('.conteudo_expansivel').show('medium');
				console.log("mostrar");
			}
			else{
				$(this).parent().children('.conteudo_expansivel').hide('medium');
				console.log("ocultar");
			}
        }
        return false;
    })
    .parent().children('.conteudo_expansivel').hide();  
};
/**************************************************************/
/* Functions to execute on loading the document               */
/**************************************************************/
$(document).ready( function() {
    prepareList()
});
