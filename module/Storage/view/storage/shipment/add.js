let isFerrous;
let shipmentUrl;
let shipmentId;
let containerId;
let containerUrl;
let containerItemUrl;

function init(val,u){
    isFerrous = val | 0;
    shipmentUrl = u;
}

function setShipmentId(id)
{
    shipmentId = id;
}

function setContainerId(id)
{
    containerId = id;
}

function setContainerUrl(url)
{
    containerUrl = url;
}

function setContainerItemUrl(url)
{
    containerItemUrl = url;
}

$(function() {
    $.ajax({
        url: shipmentUrl + "/add-shipment",
        data: { "id" : shipmentId },
        success: function(result){
            $("#tabs-1").append(result);

            if(shipmentId > 0 )
            {
                addShipment();
            }
            $("#shipment_form").submit(function(e) {
                addShipment();
                e.preventDefault();
            });
            ajaxAddContainer();
        }
    });

    function ajaxAddContainer() {
        $.ajax({
            url: containerUrl + "/add",
            data: {"container": containerId},
            success: function (result) {
                $("#tabs-2-container").append(result);

                if (containerId > 0) {
                    addContainer();
                }
                $("#container_form").submit(function (e) {
                    addContainer();
                    e.preventDefault();
                });
                ajaxAddItem();
            }
        });
    }

    function ajaxAddItem(){
        $.ajax({
            url: containerItemUrl + "/add",
            success: function (result) {
                if (isFerrous) {
                    $("#tabs-2-item").append(result);
                }
                else {
                    $("#tabs-3").append(result);
                }
                $('#save').on('click', function (event) {
                    saveShipment();
                });

                $("#item_form").submit(function (e) {
                    if (isFerrous) {
                        addWagon();
                    }
                    else {
                        addItemToContainer();
                    }
                    e.preventDefault();
                });
            }
        });
    }

    $( "#tabs" ).tabs();
    if(isFerrous)
    {
        $( "#tabs" ).tabs( "option", "disabled", [ 1 ] );
    }
    else
    {
        $( "#tabs" ).tabs( "option", "disabled", [ 1, 2 ] );
    }

});

function checkWagon()
{
    if($('#name').val())
    {
        $('.wagon').removeClass('has-error');
        return true;
    }
    $('.wagon').addClass('has-error');
    return false;
}

var shipment = {
    containers: []
};
var container = [];
var item = [];

function addShipment()
{
    if(isFerrous)
    {
        $( "#tabs" ).tabs( "option", "disabled", [ 0 ] );
    }
    else
    {
        $( "#tabs" ).tabs( "option", "disabled", [ 0, 2 ] );
    }
    $( "#tabs" ).tabs( "option", "active", 1 );
    var c_header = $('#date').val() + " : " +
        $("#trader option:selected" ).text() + " : " +
        $("#tariff option:selected" ).text();
    if($('#rate').val())
    {
        c_header += " : " + $('#rate').val();
    }

    $("#c_header").append(c_header );
    shipment = {
        date : $('#date').val(),
        trader : $("#trader").val(),
        tariff : $("#tariff").val(),
        rate : $('#rate').val(),
        containers : []
    };
}

function addWagon()
{
    if(checkWagon()) {
        $("#c_items")
            .find('tbody')
            .append($('<tr>')
                .append($('<td>').text($('#name').val()))
                .append($('<td>').text($("#metal option:selected").text() + ' ' + $("#comment").val()))
                .append($('<td>').text($("#weight").val()))
                .append($('<td>').text($("#realWeight").val()))
        );
        container = {
            name : $('#name').val(),
            owner : $("#owner" ).val(),
            items : []
        };

        item = {
            metal : $("#metal").val(),
            comment : $("#comment").val(),
            weight : $('#weight').val(),
            realWeight : $('#realWeight').val(),
            cost : $('#cost').val(),
            costDol : $('#costDol').val()
        };
        container['items'].push(item);
        shipment['containers'].push(container);
        resetWagon();
        resetItem();
    }
}

function addContainer()
{
    if (isFerrous) {
        $("#tabs").tabs("option", "disabled", [0]);
        $("#tabs").tabs("option", "active", 1);
    }
    else {
        $("#tabs").tabs("option", "disabled", [0, 1]);
        $("#tabs").tabs("option", "active", 2);
    }

    $("#c_header").append(" : "+$("#name").val() );
    container = {
        name : $('#name').val(),
        owner : $("#owner" ).val(),
        items : []
    };
    shipment['containers'].push(container);
}

function addItemToContainer()
{
    $("#c_items")
        .find('tbody')
        .append($('<tr>')
            .append($('<td>').text($("#metal option:selected").text() + ' ' + $("#comment").val()))
            .append($('<td>').text($("#weight").val()))
            .append($('<td>').text($("#realWeight").val()))
    );
    item = {
        metal : $("#metal").val(),
        weight : $('#weight').val(),
        realWeight : $('#realWeight').val(),
        cost : $('#cost').val(),
        costDol : $('#costDol').val(),
        comment : $('#comment').val()
    };

    container['items'].push(item);
    resetItem();
}

function resetWagon()
{
    $('#name').val('');
}

function resetItem()
{
    $('#comment').val('');
    $('#weight').val('');
    $('#realWeight').val('');
    $('#cost').val('');
    $('#costDol').val('');
}

function saveShipment()
{
    if(shipment.containers == '')
    {
        bootbox.alert("Нужно добавить хотя бы один вагон");
    }
    else {
        $("#save").attr("disabled", true);

        $.post( shipmentUrl + "/save-ajax", shipment,
            data => {
                console.log('save');
                window.location.replace(shipmentUrl + "/index");
                $("#save").attr("disabled", false);
            },
            'json')
            .fail( data => {
                $("#save").attr("disabled", false);
                bootbox.alert(data.responseJSON.error);
                console.log( "error" );
            });
    }
}
