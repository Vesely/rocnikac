
//Alerts
$('.alert .close').on('click', function(){
	$(this).parents('.alert').first().fadeOut(300);
	return false;
});


//Tooltips
$('.tooltip').tooltipster({
	contentAsHTML: true
});

//Data-table
$('.table-data').DataTable({
	"columns": [
		null,
		null,
		null,
		null,
		{ "orderable": false }
	],
    "bLengthChange": false,
	"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "všechny"] ],
    language: {
        search:         "Hledat:",
        lengthMenu:    "Zobraz _MENU_ testů",
        info:           "",
        loadingRecords: "Načítám obsah..",
        paginate: {
            first:      "První",
            previous:   "Předchozí",
            next:       "Následující",
            last:       "Poslední"
        }
    }
});