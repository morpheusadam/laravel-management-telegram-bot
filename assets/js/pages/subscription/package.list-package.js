"use strict";

var perscroll;
var table;

$(document).ready(function() {

    table = $("#mytable").DataTable({
        fixedHeader: false,
        colReorder: true,
        serverSide: true,
        processing:true,
        bFilter: false,
        order: [[ 1, "desc" ]],
        pageLength: 10,
        ajax:
            {
                "url": subscription_list_package_url_data,
                "type": 'POST',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                },
                data: function ( d )
                {
                    d.search_value = $('#search_value').val();
                    d.search_package_type = $('#search_package_type').val();
                }
            },
        language:
            {
                url: global_url_datatable_language
            },
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        columnDefs: [
            {
                targets: [1],
                visible: false
            },
            {
                targets: '',
                className: 'text-center'
            },
            {
                targets: [0,7],
                sortable: false
            },
            {
                targets: [3],
                "render": function ( data, type, row, meta )
                {
                    return "<span class='text-capitalize'>"+row[3]+"</span>";
                }
            },
            {
                targets: [4],
                "render": function ( data, type, row, meta )
                { 
                    if(row[3].toLowerCase()=='team') return '-';
                    else if(row[6]=="1" && row[4]=="0") return "Free";
                    else return data;
                }
            },
            {
                targets: [5],
                "render": function ( data, type, row, meta )
                {
                    if(row[3].toLowerCase()=='team') return '-';
                    else if(row[6]=="1" && row[4]=="0") return "Unlimited";
                    else return data;
                }
            },
            {
                targets: [6],
                "render": function ( data, type, row, meta )
                {
                    if(data==1) return "<i class='fas fa-check-circle text-success'></i>";
                    else return "<i class='fas fa-times-circle'></i>";
                }
            },
            {
                targets: [7],
                "render": function ( data, type, row, meta )
                {
                    var edit_url = subscription_list_package_url_update.replace(':id',row[1]);
                    var delete_url = subscription_list_package_url_delete;
                    var str="";
                    str=str+"<a class='btn btn-circle btn-outline-warning'  href='"+edit_url+"' title='"+global_lang_edit+"'>"+'<i class="fas fa-edit"></i>'+"</a>";
                    if(row[6]=='0') str=str+"&nbsp;<a href='"+delete_url+"' data-id='"+row[1]+"' title='"+global_lang_delete+"' class='btn btn-circle btn-outline-danger delete-row'>"+'<i class="fas fa-trash"></i>'+"</a>";
                    return "<div style='min-width:80px'>"+str+'</div>';

                }
            }
        ],
        fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
            if(areWeUsingScroll)
            {
                if (perscroll) perscroll.destroy();
                perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
            }
        },
        scrollX: 'auto',
        fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
            if(areWeUsingScroll)
            {
                if (perscroll) perscroll.destroy();
                perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
            }
        }
    });

    $(document).on('change', '#search_package_type', function(e) {
        table.draw(false);
    });

    $(document).on('keyup', '#search_value', function(e) {
        if(e.which == 13 || $(this).val().length>2 || $(this).val().length==0) table.draw(false);
    });
});
