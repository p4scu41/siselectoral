$(document).ready(function(){
    $form = $('<form action="" method="post" target="_blank" style="display:none">'+
            '<input type="text" name="title" id="title">'+
            '<textarea name="content" id="content"></textarea>'+
            '<input type="hidden" name="_csrf" value="'+$('[name=_csrf]').val()+'"></form>');

    $('.btnExportPdf, .btnExportExcel').click(function(event){
        $content = $('#reporteContainer').clone();

        if ($content.find('tr').find('td .btn').length != 0) {
            $content.find('tr th:last').remove();
            $content.find('tr').find('td:last').remove();
        }
        content = $content.prop('outerHTML');

        if ($(this).hasClass('btnExportExcel')) {
            content = $('#reporteContainer table').table2CSV({delivery: 'value'});
        }

        $form.find('#content').text( content );
        $form.find('#title').val( $('#titulo').html() );
        $form.attr('action', $(this).data('url'));
        $('#formExport').html($form);
        $form.submit();

        event.stopPropagation();
        event.preventDefault();
        return false;
    });

    $('#municipio').change(function(){
        $('#loadIndicator').show();
        $('.filtroEstructura').remove();

        var options = '<br class="filtroEstructura no-delete"/><div class="form-group filtroEstructura no-delete"><label>Seleccione el detalle del reporte: </label> <br class="filtroEstructura"/>&nbsp; ';

        var idMuni = $(this).val();
        self = this;

        if (idMuni != '') {
            $.getJSON(urlPuestos+'?_csrf='+$('[name=_csrf]').val()+'&idMuni='+idMuni, function(result) {
                for (var i=0; i<result.length; i++) {
                    //options += '<option value="'+result[i].IdPuesto+'" data-nivel="'+result[i].Nivel+'">'+result[i].Descripcion+'</option>';
                    options += '<div class="checkbox filtroEstructura"> &nbsp; <label>'+
                            '<input type="checkbox" name="puestos[]" value="'+result[i].IdPuesto+'" '+
                            'data-nivel="'+result[i].Nivel+'" class="chkPuesto" checked> '+
                            result[i].Descripcion+' </label> &nbsp; </div>';
                }
                options += '</div><br class="filtroEstructura no-delete"/>';
                $("#bodyForm").append(options);
                $('.chkPuesto').iCheck({
                    checkboxClass: 'icheckbox_minimal-green',
                    radioClass: 'iradio_minimal-green',
                });
                $("#bodyForm").append('<div class="form-group filtroEstructura"><label>Seleccione el nivel de estructura: </label></div><br class="filtroEstructura"/>');
            }).done(function(result) {
                if (result.length>0) {
                    id = doId(result[0].Descripcion);
                    $.post(urlNodoDepend, '_csrf='+$('[name=_csrf]').val()+'&Municipio='+idMuni,
                        function(result){ agregaPuesto.call(self, result, id); }, "json")
                        .done(function(){ $('#loadIndicator').hide(); });
                } else {
                    $('#loadIndicator').hide();
                }
            });
        } else {
            $('#loadIndicator').hide();
        }
    });

    $('#btnGenerarReporte, #btnReporteSeccional, #btnReporteAuditoria').click(function(event) {
        $('.alert').remove();

        if ($('#municipio').val() == '') {
            $('#bodyForm').append('<div class="alert alert-danger" role="alert">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span></button>Debe seleccionar un municipio</div>');
            return false;
        }

        tipoReporte = '';
        $this = $(this);

        if ($(this).prop('id') == 'btnReporteSeccional') {
            tipoReporte = 1;
        } else if ($(this).prop('id') == 'btnGenerarReporte') {
            tipoReporte = 2;
        } else if ($(this).prop('id') == 'btnReporteAuditoria') {
            tipoReporte = 4;
        }

        $('#loadIndicator').show();
        $('#div_loading').show();

        $.ajax({
            type: 'POST',
            url: urlReporte,
            data: $('#formBuscar').serialize()+'&tipoReporte='+tipoReporte,
            dataType: 'json',
            success: function(result) {
                $('#titulo').html(result.titulo);
                $('#tabla_reporte').html(result.reporteHTML);

                $('#tabla_reporte tr td:nth-child(3)').css('cursor', 'pointer');
                // Agrega el evento clic en el nombre de la persona asignada al puesto
                $('#tabla_reporte tr td:nth-child(3)').on('click', function(event){
                    console.log($(this).text());
                });

                if ($this.prop('id') == 'btnGenerarReporte') {
                    // Agrega la columna de auditoria
                    $('#tabla_reporte thead tr').append('<th class="text-center">Auditoría</th>');
                    $('#tabla_reporte tbody tr').append('<td class="text-center"><a class="btn btn-default btnAuditar" title="Auditar Datos"><span class="glyphicon glyphicon glyphicon-th-list" aria-hidden="true"></span></a></td>');
                }

                $('#loadIndicator').hide();
                $('.opcionesExportar').show();
                $('#div_loading').fadeOut('slow');
            }
        });
    });

    $('#btnResumen').click(function() {
        $('.alert').remove();

        if ($('#municipio').val() == '') {
            $('#bodyForm').append('<div class="alert alert-danger" role="alert">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span></button>Debe seleccionar un municipio</div>');
            return false;
        }

        $('#loadIndicator').show();
        $('#modalResumen .table-responsive').html('<i class="fa fa-refresh fa-spin text-center" style="font-size: x-large; display: inline-block;"></i>');
        $.ajax({
            url: urlResumen,
            dataType: "json",
            data: {_csrf: $('[name=_csrf]').val() ,idMuni: $('#municipio').val()},
            type: "GET",
        }).done(function(response){
            $('#loadIndicator').hide();
            if (response.length == 0) {
                $('#alertResult').html('No se encontraron resultados en la b&uacute;squeda');
                $('#alertResult').show();
            } else {
                var fecha = new Date();
                $('#alertResult').hide();
                tablaResumen = $(ConvertJsonToTable(response, 'tablaResumen', 'table table-condensed table-striped table-bordered table-hover', 'Download'));
                $('#modalResumen .table-responsive').html(tablaResumen);

                $('<tr><td colspan="5">&nbsp;</td></tr>').insertBefore( tablaResumen.find('tr:last'));

                $('#tituloResumen').html(' Municipal de '+$('#municipio option:selected').text());
                $('#fechaResumen').html('Fecha de corte: '+padLeft(fecha.getDate(),2)+'-'+padLeft((fecha.getMonth()+1),2)+'-'+fecha.getFullYear());
                $('#modalResumen').modal('show');
            }
        });
    });

    $('#reporteContainer').on('click', '.btnAuditar', function (event) {
        event.preventDefault();
        event.stopPropagation();

        id = $(this).closest('tr').data('id');
        $this = $(this);
        iconCache = $(this).html();
        $(this).html('<i class="fa fa-spinner fa-pulse"></i>');

        $.ajax({
            url: urlGetAuditoria,
            dataType: "json",
            data: {_csrf: $('[name=_csrf]').val() ,id: id},
            type: "POST",
        }).done(function(response){
            $this.html(iconCache);
            $('#formAuditoria').trigger('reset');
            $('#label_fecha').html('');
            $('#formAuditoria input[type="checkbox"]').each(function(){ this.checked = false; });
            $('#formAuditoria .icheckbox_minimal').removeClass('checked');
            $('#formAuditoria [name=Puesto]').iCheck('uncheck');
            $('#formAuditoria [name=Persona]').iCheck('uncheck');
            $('#formAuditoria [name=Seccion]').iCheck('uncheck');
            $('#formAuditoria [name=Celular]').iCheck('uncheck');

            $('#formAuditoria [name=IdNodoEstructuraMov]').val(id);

            if (response.auditoria != null) {
                var fecha = moment(response.auditoria.Fecha).format('DD-MM-YYYY hh:mm a');
                $('#label_fecha').html(fecha);

                if (response.auditoria.Puesto == 1) {
                    $('#formAuditoria [name=Puesto]').iCheck('check');
                }

                if (response.auditoria.Persona == 1) {
                    $('#formAuditoria [name=Persona]').iCheck('check');
                }

                if (response.auditoria.Seccion == 1) {
                    $('#formAuditoria [name=Seccion]').iCheck('check');
                }

                if (response.auditoria.Celular == 1) {
                    $('#formAuditoria [name=Celular]').iCheck('check');
                }

                $('#formAuditoria [name=Observaciones]').val(response.auditoria.Observaciones);
            }

            $('#label_puesto').html($('tr[data-id="'+id+'"]').find('td:nth-child(1)').text());
            $('#label_descripcion').html($('tr[data-id="'+id+'"]').find('td:nth-child(2)').text());
            $('#label_nombre').html($('tr[data-id="'+id+'"]').find('td:nth-child(3)').text());
            $('#label_seccion').html($('tr[data-id="'+id+'"]').find('td:nth-child(4)').text());
            $('#label_celular').html($('tr[data-id="'+id+'"]').find('td:nth-child(10)').text());

            $('#modalAuditar').modal('show');
        });
    });

    $('#modalAuditar').on('click', '.btn-success', function (event) {
        event.preventDefault();
        event.stopPropagation();

        $this = $(this);
        iconCache = $(this).html();
        $(this).html('<i class="fa fa-spinner fa-pulse"></i>');

        $.ajax({
            url: urlSetAuditoria,
            dataType: "json",
            data: $('#formAuditoria').serialize(),
            type: "POST",
        }).done(function(response){
            $this.html(iconCache);
            
            $('#modalAuditar').modal('hide');
        });
    });
});