
    $(function(){
        $('#ttienda').autocomplete({
            source : 'ajax1.php',
            select : function(event, ui){  
                $('#ntienda').html (
                    '<input name="idti"  type="hidden" value="'+ui.item.idt+'">'
                ); 
            }
        });
    }); 
    $(function(){
        $('#ttienda2').autocomplete({
            source : 'ajax1.php',
            select : function(event, ui){  
                $('#ntienda2').html (
                    '<input name="idti2"  type="hidden" value="'+ui.item.idt+'">'
                ); 
            }
        });
    }); 
    
    $(function(){
        $('#tusuario').autocomplete({
            source : 'ajax2.php',
            select : function(event, ui){  
                $('#nusuario').html (
                    '<input name="tidus"  type="hidden" value="' + ui.item.idu+ '">' +
                    '<input class="decorar-input" name="tnombreu" required type="tex" title="Nombre Completo del Usuario"  value="' + ui.item.nomu + '">'+  
                    '<input class="decorar-input" name="tuseru" required type="text" title="Usuario"  value="' + ui.item.useru + '">'+ 
                    '<input class="decorar-input" name="tpassu" required type="tex" title="Contraseña"  value="' + ui.item.passu + '">'+  
                    '<input class="decorar-input" name="tpusuario" required type="text" title="Tipo Usuario"  value="' + ui.item.tipou + '">'+ 
                    '<input class="decorar-input" name="idti" required type="text" title="Número Tienda"  value="' + ui.item.tieu + '">'+ 
                    '<div id="botones"><button type="submit" name="updateu" class="btn-universal"><span class="icon icon-pencil"></span></button>' +
                    '<button type="submit" name="deleteu" class="btn-universal"><span class="icon icon-bin"></span></button></div>'                       
                ); 
            }
        });
    }); 
    
    $(function(){
        $('#tpaciente').autocomplete({
            source : 'ajax3.php',
            select : function(event, ui){  
                $('#npaciente').html (
                    '<input name="tidpa"  type="hidden" value="' + ui.item.idpa+ '">' +
                    '<input class="decorar-input" name="tnitp" type="tex" title="NIT o DPI del Paciente"  value="' + ui.item.nitpa + '">'+  
                    '<input class="decorar-input" name="tnombrep" required type="text" title="Nombre Completo del Paciente"  value="' + ui.item.nompa + '">'+ 
                    '<input class="decorar-input" name="ttelefonop" required type="text" title="Numero de Telefono del Paciente Ocho digitos sin espacio"  value="' + ui.item.telpa + '">'+  
                    '<div id="botones"><button type="submit" name="upinaten" class="btn-universal"><span class="icon icon-checkmark"></span></button></div>'                       
                ); 
            }
        });
    }); 
    
    $(function(){
        $('#bpaciente').autocomplete({
            source : 'ajax3.php',
            select : function(event, ui){  
                $('#npaciente').html (
                    '<input name="tidpa"  type="hidden" value="' + ui.item.idpa+ '">'                   
                ); 
            }
        });
    }); 
    
    $(function(){
        $('#tlente').autocomplete({
            source : 'ajax4.php',
            select : function(event, ui){  
                $('#nlente').html (
                    '<input name="tidpro"  type="hidden" value="' + ui.item.idpro+ '">' +
                    '<input name="tppro"  type="hidden" value="' + ui.item.preciop+ '">' 
                ); 
            }
        });
    }); 
    
     $(function(){
        $('#tlenm').autocomplete({
            source : 'ajax4.php',
            select : function(event, ui){  
                $('#nlenm').html (
                    '<input name="tidlen"  type="hidden" value="' + ui.item.idpro+ '">' +
                    '<input class="decorar-input" name="tdeslen"  type="text" title="Descripcion del Lente (Marca, Color, Otro)" required  value="' + ui.item.descp+ '">' +
                    '<input class="decorar-input" name="tgalen"  type="text" title="Gamma del Lente (Premium, Media, Alta)" required  value="' + ui.item.clap+ '">' +
                    '<input class="decorar-input" name="tprelen"  type="text" title="Precio al Publico" required value="' + ui.item.preciop+ '">' +
                    '<input class="decorar-input" name="tobslen"  type="text" title="Observacion para el Lente" value="' + ui.item.obsp+ '">' +
                    '<div id="botones"><button type="submit" name="updlen" class="btn-universal"><span class="icon icon-pencil"></span></button>' +
                    '<button type="submit" name="dellen" class="btn-universal"><span class="icon icon-bin"></span></button></div>' 
                ); 
            }
        });
    }); 
    
    $(function(){
        $('#tmedic').autocomplete({
            source : 'ajax5.php',
            select : function(event, ui){  
                $('#nmedic').html (
                    '<input name="tidpro2"  type="hidden" value="' + ui.item.idpro2+ '">' +
                    '<input name="tppro2"  type="hidden" value="' + ui.item.preciop2+ '">' 
                ); 
            }
        });
    });
    
    $(function(){
        $('#tmedm').autocomplete({
            source : 'ajax5.php',
            select : function(event, ui){  
                $('#nmedm').html (
                    '<input name="tidmed"  type="hidden" value="' + ui.item.idpro2+ '">' +
                    '<input class="decorar-input" name="tdesmed"  type="text" title="Descripcion del Medicamento (Marca, Presentacion, Otro)" required  value="' + ui.item.descp2+ '">' +
                    '<input class="decorar-input" name="tvenmed"  type="date" required  value="' + ui.item.venp2+ '">' +
                    '<input class="decorar-input" name="tpremed"  type="text" title="Precio al Publico" required value="' + ui.item.preciop2+ '">' +
                    '<input class="decorar-input" name="tobsmed"  type="text" title="Observacion para el Medicamento" value="' + ui.item.obsp2+ '">' +
                    '<div id="botones"><button type="submit" name="updmed" class="btn-universal"><span class="icon icon-pencil"></span></button>' +
                    '<button type="submit" name="delmed" class="btn-universal"><span class="icon icon-bin"></span></button></div>' 
 
                ); 
            }
        });
    }); 
    
    $(function(){
        $('#taro').autocomplete({
            source : 'ajax6.php',
            select : function(event, ui){  
                $('#naro').html (
                    '<input name="tidpro3"  type="hidden" value="' + ui.item.idpro3+ '">' +
                    '<input name="tppro3"  type="hidden" value="' + ui.item.preciop3+ '">' 
                ); 
            }
        });
    }); 
    
    $(function(){
        $('#tarom').autocomplete({
            source : 'ajax6.php',
            select : function(event, ui){  
                $('#narom').html (
                    '<input name="tidaro"  type="hidden" value="' + ui.item.idpro3+ '">' +
                    '<input class="decorar-input" name="tdesaro"  type="text" title="Descripcion del Aro (Marca, Color, Otro)" required  value="' + ui.item.descp3+ '">' +
                    '<input class="decorar-input" name="tdaro"  type="text" title="Detalle del Aro" required value="' + ui.item.clap3+ '">' +
                    '<input class="decorar-input" name="tprearo"  type="text" title="Precio al Publico" required value="' + ui.item.preciop3+ '">' +
                    '<input class="decorar-input" name="tobsaro"  type="text" title="Observacion para el Aro" value="' + ui.item.obsp3+ '">' +
                    '<div id="botones"><button type="submit" name="updaro" class="btn-universal"><span class="icon icon-pencil"></span></button>' +
                    '<button type="submit" name="delaro" class="btn-universal"><span class="icon icon-bin"></span></button></div>' 
                ); 
            }
        });
    }); 
    
    $(function(){
        $('#tservi').autocomplete({
            source : 'ajax7.php',
            select : function(event, ui){  
                $('#nservi').html (
                    '<input name="tidpro4"  type="hidden" value="' + ui.item.idpro4+ '">' +
                    '<input name="tppro4"  type="hidden" value="' + ui.item.preciop4+ '">' 
                ); 
            }
        });
    }); 
    
    $(function(){
        $('#tserm').autocomplete({
            source : 'ajax7.php',
            select : function(event, ui){  
                $('#nserm').html (
                    '<input name="tidser"  type="hidden" value="' + ui.item.idpro4+ '">' +
                    '<input class="decorar-input" name="tdesser"  type="text" title="Descripcion del Servicio" required  value="' + ui.item.descp4+ '">' +
                    '<input class="decorar-input" name="tpreser"  type="text" title="Precio al Publico" required value="' + ui.item.preciop4+ '">' +
                    '<input class="decorar-input" name="tobsser"  type="text" title="Observacion para el Servicio" value="' + ui.item.obsp4+ '">' +
                    '<div id="botones"><button type="submit" name="updser" class="btn-universal"><span class="icon icon-pencil"></span></button>' +
                    '<button type="submit" name="delser" class="btn-universal"><span class="icon icon-bin"></span></button></div>'  
                ); 
            }
        });
    }); 
    
    $(function(){
        $('#tac').autocomplete({
            source : 'ajax8.php',
            select : function(event, ui){  
                $('#nac').html (
                    '<input name="tidpro5"  type="hidden" value="' + ui.item.idpro5+ '">' +
                    '<input name="tppro5"  type="hidden" value="' + ui.item.preciop5+ '">' 
                ); 
            }
        });
    }); 
    
    $(function(){
        $('#tacm').autocomplete({
            source : 'ajax8.php',
            select : function(event, ui){  
                $('#nacm').html (
                    '<input name="tidac"  type="hidden" value="' + ui.item.idpro5+ '">' +
                    '<input class="decorar-input" name="tdesac"  type="text" title="Descripcion del Servicio" required  value="' + ui.item.descp5+ '">' +
                    '<input class="decorar-input" name="tpreac"  type="text" title="Precio al Publico" required value="' + ui.item.preciop5+ '">' +
                    '<input class="decorar-input" name="tobsac"  type="text" title="Observacion para el Servicio" value="' + ui.item.obsp5+ '">' +
                    '<div id="botones"><button type="submit" name="updac" class="btn-universal"><span class="icon icon-pencil"></span></button>' +
                    '<button type="submit" name="delac" class="btn-universal"><span class="icon icon-bin"></span></button></div>'  
                ); 
            }
        });
    }); 
    
     $(function(){
        $('#trep').autocomplete({
            source : 'ajax13.php',
            select : function(event, ui){  
                $('#nrep').html (
                    '<input name="tidpro6"  type="hidden" value="' + ui.item.idpro6+ '">' +
                    '<input name="tppro6"  type="hidden" value="' + ui.item.preciop6+ '">' 
                ); 
            }
        });
    });
    
    $(function(){
    $('#tbuscarp').autocomplete({
         source : 'ajax9.php',
        select : function(event, ui){
            $('#bpro').slideUp('slow', function (){
                $('#bpro').html(
                '<h3>INFORMACIÓN DEL PRODUCTO</h3>' +
                '<table class="tablaA">' +
                '<tr><td width = "35%">ID:</td><td>' + ui.item.idp + '</td></tr>' +
                '<tr><td>TIPO:</td><td>' + ui.item.tipo + '</td></tr>' +
                '<tr><td>CLASE:</td><td>' + ui.item.clase + '</td></tr>' +
                '<tr><td>DESCRIPCIÓN:</td><td>' + ui.item.desc + '</td></tr>' +
                '<tr><td>VENCIMIENTO:</td><td>' + ui.item.fven + '</td></tr>' +
                '<tr><td>PRECIO:</td><td> Q ' + ui.item.precio + '</td></tr>' +
                '<tr><td>OBSERVACIÓN:</td><td>' + ui.item.obs + '</td></tr>' +
                '<tr><td>MEGA OPTICA:</td><td>' + ui.item.t1 + ' Unidades</td></tr>' +
                '<tr><td>LENTES Y GAFAS:</td><td>' + ui.item.t2 + ' Unidades</td></tr>' +
                '<tr><td>MACARIO JUNIOR:</td><td>' + ui.item.t3 + ' Unidades</td></tr>' +
                '<tr><td>MACARIO SIMOCOL:</td><td>' + ui.item.t4 + ' Unidades</td></tr>' +
                '<tr><td>MACARIO SOLOLÁ:</td><td>' + ui.item.t5 + ' Unidades</td></tr>' +
                '<tr><td>MACARIO HUEHUETENANGO:</td><td>' + ui.item.t6 + ' Unidades</td></tr>' +
                '<tr><td>MACARIO TECPÁN:</td><td>' + ui.item.t7 + ' Unidades</td></tr>' +
                '<tr><td>MACARIO EL CARRIZAL:</td><td>' + ui.item.t8 + ' Unidades</td></tr>' + 
                '<tr><td>BODEGA:</td><td>' + ui.item.bod + ' Unidades</td></tr>' +
                '</table>'                
                );
            });
            $('#bpro').slideDown('slow');                                         
        }
    });
    } );
    
    $(function(){
        $('#tbuscarp2').autocomplete({
            source : 'ajax9.php',
            select : function(event, ui){  
                $('#bpro2').html (
                    '<input name="idp2"  type="hidden" value="' + ui.item.idp + '">' 
                ); 
            }
        });
    }); 
    
    $(function(){
        $('#lder').autocomplete({
            source : 'ajax10.php',
            select : function(event, ui){  
                $('#nder').html (
                    '<input name="idled"  type="hidden" value="' + ui.item.idl+ '">' +
                    '<input name="idpld"  type="hidden" value="' + ui.item.prel+ '">' 
                ); 
            }
        });
    });
    
    $(function(){
        $('#lizq').autocomplete({
            source : 'ajax10.php',
            select : function(event, ui){  
                $('#nizq').html (
                    '<input name="idlei"  type="hidden" value="' + ui.item.idl+ '">' +
                    '<input name="idpli"  type="hidden" value="' + ui.item.prel+ '">' 
                ); 
            }
        });
    }); 
    
    $(function(){
        $('#tprolab').autocomplete({
            source : 'ajax11.php',
            select : function(event, ui){  
                $('#nprolab').html (
                    '<input name="idpl"  type="hidden" value="' + ui.item.idln+ '">' +
                    '<input name="idcl"  type="hidden" value="' + ui.item.preln+ '">' 
                ); 
            }
        });
    }); 
    
    $(function(){
        $('#tproml1').autocomplete({
            source : 'ajax11.php',
            select : function(event, ui){  
                $('#nproml1').html (
                    '<input name="idpl1"  type="hidden" value="' + ui.item.idln+ '">' 
                ); 
            }
        });
    }); 
    
    $(function(){
        $('#tproml2').autocomplete({
            source : 'ajax11.php',
            select : function(event, ui){  
                $('#nproml2').html (
                    '<input name="idpl2"  type="hidden" value="' + ui.item.idln+ '">'
                ); 
            }
        });
    }); 
    
    $(function(){
        $('#taten').autocomplete({
            source : 'ajax12.php',
            select : function(event, ui){ 
                $('#naten').slideUp('slow', function (){
                    $('#naten').html (
                    '<input name="idat"  type="hidden" value="' + ui.item.ida + '">' +
                    '<textarea class="decorar-input" name="tobsv"  id="tobsv" placeholder="Observaciones en las Ventas" required rows="5">'+ ui.item.obsv +'</textarea>'
                    );
                });
            $('#naten').slideDown('slow');                                         
            }
        });
    } );

    
    $(function(){
        $('#taten2').autocomplete({
            source : 'ajax12.php',
            select : function(event, ui){  
                $('#naten2').html (
                    '<input name="idat2"  type="hidden" value="' + ui.item.ida + '">'
                ); 
            }
        });
    }); 