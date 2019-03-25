$(document).ready(function() {
    $('.DatepickerField').datetimepicker({ 
							dateFormat: 'dd/mm/yy',
							timeFormat: "HH:mm:ss",
							//minDate: new Date(2011, 1, 10, 0, 0),                                        
							changeMonth: true,
							changeYear: true,
							addSliderAccess: true,
							sliderAccessArgs: {touchonly: false},
							showOtherMonths: true,                    
							selectOtherMonths: true
						});
    
    
    activeDataTable();
});

function activeDataTable(){
	$('.datatable').DataTable({
    	searching: true,
        ordering:  true,
        buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
        colReorder: true
    
	});
    $('.datatable').show();
}

function retira_acentos(s){
	var r=s/*.toLowerCase()*/;
    r = r.replace(new RegExp(/[àáâãäå]/g),"a");
    r = r.replace(new RegExp(/[èéêë]/g),"e");
    r = r.replace(new RegExp(/[ìíîï]/g),"i");
    r = r.replace(new RegExp(/[òóôõö]/g),"o");
    r = r.replace(new RegExp(/[ùúûü]/g),"u");
    //r = r.replace(new RegExp(/[ýÿ]/g),"y");
    return r;
};

//exporta a tabela dvData
function exportaTabela(id_tabela) {
        //getting values of current time for generating the file name
        var dt = new Date();
        var day = dt.getDate();
        var month = dt.getMonth() + 1;
        var year = dt.getFullYear();
        var hour = dt.getHours();
        var mins = dt.getMinutes();
        var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
        //creating a temporary HTML link element (they support setting file names)
        var a = document.createElement('a');
        //getting data from our div that contains the HTML table
        var data_type = 'data:application/vnd.ms-excel;charset=UTF-8';
        var table_div = document.getElementById(id_tabela);
        var table_html = retira_acentos(table_div.outerHTML.replace(/ /g, '%20'));
        a.href = data_type + ', ' + table_html;
        //setting the file name
        a.download = 'exported_table_' + postfix + '.xls';
        //triggering the function
        a.click();
        //just in case, prevent default behaviour
        e.preventDefault();
    }
