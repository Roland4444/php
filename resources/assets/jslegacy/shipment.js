shipment = {
    shipment : [],
    container : [],
    items : [],
    shipmentPlus : 0,
    containerPlus : 0,

    init : function(shipmentPlus,containerPlus){
        $('.container_div').hide();
        $('.container_item_div').hide();
        $('.container_items').hide();
        $('.container_h').hide();
        this.shipmentPlus = shipmentPlus | 0;
        this.containerPlus = containerPlus | 0;
        if(shipmentPlus){
            this.addShipment();
        }
        if(containerPlus){
            this.addContainer();
        }
    },

    addShipment : function(){
        $("#shipment_h").append(
            $('#date').val() + " : " +
                $("#trader option:selected" ).text() + " : " +
                $("#tariff option:selected" ).text() + " : " +
                $('#rate').val()
        );

        shipment = {
            date : $('#date').val(),
            trader : $("#trader").val(),
            tariff : $("#tariff").val(),
            rate : $('#rate').val(),
            containers : []
        };

        $(".shipment_div").hide();
        $(".container_div").show();
        if($('#type').val() == 'black')
        {
            $(".container_item_div").show();
        }
    },

    checkContainer : function(){
        if($('#name').val())
        {
            $('.container').removeClass('error');
            return true;
        }
        $('.container').addClass('error');
        return false;
    },

    checkItem : function(){
        if($('#weight').val() && $('#realWeight').val())
        {
            $('.items').removeClass('error');
            return true;
        }
        $('.items').addClass('error');
        return false;
    },

    addContainer : function(){
        if(this.checkContainer())
        {
            $("#container_h")
                .find('tbody')
                .append($('<tr>')
                    .append($('<td>').text($('#name').val()) )
                    .append($('<td>').text($("#owner option:selected" ).text()))
                    .append($('<td>').text($("#sez option:selected" ).text()))
                );

            container = {
                name : $('#name').val(),
                owner : $("#owner" ).val(),
                items : []
            };

            shipment['containers'].push(container);

            $('#name').val('');
            $(".container_div").hide();
            $(".container_item_div").show();
            $('.container_h').show();
        }
    },

    addItem : function(){
        if(this.checkItem())
        {
            $(".container_items").show();

            $("#container_items").find('tbody')
                .append($('<tr>')
                    .append($('<td>').text($("#metal option:selected" ).text()))
                    .append($('<td>').text($('#weight').val() ))
                    .append($('<td>').text($('#realWeight').val() ))
                    .append($('<td>').text($('#cost').val() ))
                    .append($('<td>').text($('#costDol').val() ))
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

            $('#weight').val('');
            $('#realWeight').val('');
            $('#cost').val('');
            $('#costDol').val('');
            $('#comment').val('')
        }
    },
    addWagon : function(){
        if(this.checkContainer() && this.checkItem())
        {
            $('.container_h').show();

            $("#container_h")
                .find('tbody')
                .append($('<tr>')
                    .append($('<td>').text($('#name').val()) )
                    .append($('<td>').text($("#owner option:selected" ).text()))
                    .append($('<td>').text($("#sez option:selected" ).text()))
                );

            container = {
                name : $('#name').val(),
                owner : $("#owner" ).val(),
                items : []
            };

            shipment['containers'].push(container);

            item = {
                metal : $("#metal").val(),
                weight : $('#weight').val(),
                realWeight : $('#realWeight').val(),
                cost : $('#cost').val(),
                costDol : $('#costDol').val()
            };

            container['items'].push(item);

            $('#weight').val('');
            $('#realWeight').val('');
            $('#cost').val('');
            $('#costDol').val('');
            $('#name').val('');
        }
    },
    end : function(){
        window.location.replace("../shipment");
    },
    editItem : function(dep,id){
        $.ajax({
            url: "/dep"+dep+"/storage/container-item/edit/"+id
        })
        .done(function( data ) {
            $("#editItemDialog").html(data);
        });
        $("#editItemDialog").dialog({modal:true,minWidth: 200, minHeight: 400});
    },
    saveItem : function(dep,id){
        const self = this;
        $.post("/dep"+dep+"/storage/container-item/edit/"+id, $("form").serialize(),
            () => {
                console.log('Item save is successful');
                self.updateItemHtml(dep, id);
            }, 'json'
        ).fail(() => {
            console.log('could not save item');
        });
        $("#editItemDialog").dialog('close');
    },
    updateItemHtml: function (dep, id) {
        $.post("/dep"+dep+"/storage/shipment/get-item/"+id,
            {
                startdate: $('startdate').val(),
                enddate: $('enddate').val(),
                trader: $('trader').val()
            },
            data => {
                console.log('get item');
                let i = 0;
                const item = data.item;
                $("#row"+id).find('td').each (function() {
                    i++;
                    switch(i){
                        case 1:$(this).html(item.metal);
                            break;
                        case 2:$(this).html(item.weight);
                            break;
                        case 3:$(this).html(item.actual);
                            break;
                        case 4: $(this).html(item.cost);
                            break;
                        case 5: $(this).html(item.sum);
                            break;
                    }
                    $(this).addClass("selected");
                });
                i = 0;
                const shipment = data.shipment;
                $("#shipment"+shipment.id).find('th').each (function() {
                    i++;
                    switch(i){
                        case 2:$(this).html(shipment.weight);
                            break;
                        case 3:$(this).html(shipment.actual);
                            break;
                        case 6:
                            $(this).html(shipment.sum);
                            break;
                    }

                });
                i = 0;
                const container = data.container;
                $("#container"+container.id).find('th').each (function() {
                    i++;
                    switch(i){
                        case 2:$(this).html(container.weight);
                            break;
                        case 3:$(this).html(container.actual);
                            break;
                        case 6: $(this).html(container.sum);
                            break;
                    }
                });
                const total = data.total;
                $('#total_weight').html(total.weight);
                $('#total_actual').html(total.actual);
                $('#total_sum').html(total.sum);

            }, 'json'
        ).fail(() => {
            console.log('could not get item');
        });
    }
};
