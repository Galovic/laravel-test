(function($) {

    // Setting datatable defaults
    $.extend( $.fn.DataTable.defaults, {
        autoWidth: false,
        columnDefs: [{ 
            orderable: false,
            width: '100px',
        }],
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
        language: {
            search: '<span>Filtr:</span> _INPUT_',
            lengthMenu: '<span>Zobrazit:</span> _MENU_',
            paginate: { 'first': 'První', 'last': 'Poslední', 'next': '&rarr;', 'previous': '&larr;' },
            zeroRecords: 'Nebyly nalezeny žádné záznamy',
            emptyTable: 'Nebyly nalezeny žádné záznamy',
            info: 'Zobrazuji _PAGE_ záznamů z _PAGES_',
            infoEmpty: 'Nebyly nalezeny žádné záznamy',
            infoFiltered: '(filtrováno z celkového počtu _MAX_ záznamů)',
            searchPlaceholder: 'Vepište hledaný řetězec...'
        },
        drawCallback: function () {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
        },
        preDrawCallback: function() {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
        }
    });


    // Multi column ordering
    $('.datatable-multi-sorting').DataTable({
        columnDefs: [{
            targets: [0],
            orderData: [0, 1]
        }, {
            targets: [1],
            orderData: [1, 0]
        }, {
            targets: [4],
            orderData: [4, 0]
        }, {
            orderable: false,
            width: '100px',
            targets: [5]
        }]
    });


    // Complex headers with sorting
    $('.datatable-complex-header').DataTable({
        columnDefs: []
    });


    // Sequence control
    $('.datatable-sequence-control').dataTable( {
        "aoColumns": [
            null,
            null,
            {"orderSequence": ["asc"]},
            {"orderSequence": ["desc", "asc", "asc"]},
            {"orderSequence": ["desc"]},
            null
        ]
    });
})(jQuery);
