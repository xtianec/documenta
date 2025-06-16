$(document).ready(function () {
    // Inicializar las dos tablas
    listarPuestosConDocumentos();
    listarPuestosSinDocumentos();

    // Función para listar los puestos con documentos asignados
    function listarPuestosConDocumentos() {
        $('#tblPuestosConDocumentos').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: '/documenta/controlador/DocumentMandatoryController.php?op=listarPuestosConDocumentosPorEmpresaCompleto',
                type: "GET",
                dataType: "json",
                error: function (e) {
                    console.error(e.responseText);
                }
            },
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "No hay registros disponibles",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "bDestroy": true,
            "order": [[0, "asc"]]
        });
    }

    // Función para listar los puestos sin documentos asignados
    function listarPuestosSinDocumentos() {
        $('#tblPuestosSinDocumentos').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: '/documenta/controlador/DocumentMandatoryController.php?op=listarPuestosSinDocumentos',
                type: "GET",
                dataType: "json",
                error: function (e) {
                    console.error(e.responseText);
                }
            },
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "No hay registros disponibles",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "bDestroy": true,
            "order": [[0, "asc"]]
        });
    }
});
