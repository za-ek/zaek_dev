<?php
$this->template()->addCss('\jQuery.fileTree.css');
$this->template()->addJs('\jQuery.fileTree.js');

$root_dir = $this->conf()->get('repo', 'dir');
?>
<section class="content">
    <div class="row">
        <div class="col-lg-4">
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title">Файловая система</h3>
                </div>
                <div class="box-body">
                    <div id="file_tree"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-2">
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title">Теги <span id="file_path"></span></h3>
                </div>
                <div class="box-body fluid">
                    <ul class="full_width_list" id="tag_list" data-zlist-language="rus">
                        <?php
                        exec('cd '.$root_dir.' && for i in $(ls -d */); do echo ${i%%/}; done', $aModules);
                        foreach ( $aModules as $module ) {
                            ?>
                            <li data-zlist-id="<?=$module?>" data-zlist-title="<?=$module?>"><a href="#"><?=$module?></a></li>
                            <?php
                        }
                        ?>
                        <li data-zlist-id="ignore" data-zlist-title="Игнорировать"><a href="#">Игнорировать</a></li>
                    </ul>
                </div>
                <script>
                    $(function() {
                        $('#tag_list li').on('click', function(e) {
                            $.post('/zaek/admin/dev/set_tag.php', {
                                path:$('#file_path').text(),
                                module:$(this).attr('data-zlist-id')
                            }, function(e) {
                                $('#tag_overlay').hide();
                            });
                        });
                    });
                </script>
                <div class="overlay" id="tag_overlay" style="display:none;"><i class="fa fa-spin fa-refresh"></i></div>
            </div>
        </div>
        <div class="col-lg-2">
            <style type="text/css">
                #make_new_version {margin:30px auto 0 auto;}
                .red_alert_button {height:140px;width:70px;}
                .red_alert_button {cursor:pointer;}
                .red_alert_button > .button_btn  {height:60px;}
                .red_alert_button > .button_btn > div {width:60px;position:absolute;background-color:#f00;border:1px solid #000;}
                .red_alert_button > .button_btn > .button_btn_top {height:30px;border-radius:60px / 30px;z-index:100;}
                .red_alert_button > .button_btn > .button_btn_body {height:30px;margin-top:15px;z-index:99;border-bottom:0;}
                .red_alert_button > .button_btn > .button_btn_bottom {height:30px;border-radius:60px / 30px;z-index:98;margin-top:30px;}

                .red_alert_button > .button_btn:hover > .button_btn_top {-webkit-box-shadow: inset 0px 0px 17px -4px rgba(0,0,0,0.75);-moz-box-shadow: inset 0px 0px 17px -4px rgba(0,0,0,0.75);box-shadow: inset 0px 0px 17px -4px rgba(0,0,0,0.75);}
                .red_alert_button > .button_base {margin-top:-30px;}
                .red_alert_button > .button_base > .button_base_top,
                .red_alert_button > .button_base > .button_base_bottom {
                    -webkit-transform: skew(-20deg);
                    -moz-transform: skew(-20deg);
                    -o-transform: skew(-20deg);
                    position:absolute;
                    width:100px;
                    height:40px;
                    border:1px solid #000;
                    margin-left:-20px;
                    background-color:#8c6060;
                }
                .red_alert_button > .button_base > .button_base_top {z-index:97;}
                .red_alert_button > .button_base > .button_base_body {height:30px;z-index:96;width:101px;background-color:#8c6060;margin:40px 0 0 -27.5px;position:absolute;
                    border-left:1px solid #333;
                    -webkit-box-shadow: inset 0px 5px 17px -4px rgba(0,0,0,0.75);
                    -moz-box-shadow: inset 0px 5px 17px -4px rgba(0,0,0,0.75);
                    box-shadow: inset 0px 5px 17px -4px rgba(0,0,0,0.75);
                }
                .red_alert_button > .button_base > .button_base_right {z-index:196;position:absolute;margin:0;background-color:#8c6060;
                    -webkit-transform: skewY(-70deg);
                    -moz-transform: skewY(-70deg);
                    -o-transform: skewY(-70deg);
                    width:15px;height:30px;
                    margin:21px 0 0 72px;
                    -webkit-box-shadow: inset 0px 5px 17px -4px rgba(0,0,0,0.75);
                    -moz-box-shadow: inset 0px 5px 17px -4px rgba(0,0,0,0.75);
                    box-shadow: inset 0px 5px 17px -4px rgba(0,0,0,0.75);
                    border-top:1px solid #000;
                    border-left:1px solid #444;
                }
                .red_alert_button > .button_base > .button_base_bottom {z-index:95;margin-top:30px;}


            </style>
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title">Версии </h3>
                </div>
                <div class="box-body fluid">
                    <div class="red_alert_button" id="make_new_version">
                        <div class="button_btn">
                            <div class="button_btn_top"></div>
                            <div class="button_btn_body"></div>
                            <div class="button_btn_bottom"></div>
                        </div>
                        <div class="button_base">
                            <div class="button_base_top"></div>
                            <div class="button_base_body"></div>
                            <div class="button_base_right"></div>
                            <div class="button_base_bottom"></div>
                        </div>
                    </div>
                </div>
                <div class="overlay" id="btn_overlay" style="display:none;"><i class="fa fa-spin fa-refresh"></i></div>
            </div>

        </div>
        <script type="text/javascript">
            $('#make_new_version').click(function() {
                $('#btn_overlay').show();
                $.post('/zaek/admin/dev/no_tag.php', {zAjax:true, zAjaxType:'json'}, function(d) {
                    d = JSON.parse(d);
                    if ( d.length ) {
                        $('#file_not_assigned').html('');
                        $(d).each(function(k,v) {
                            $('#file_not_assigned').append('<li>'+v+'</li>');
                        });
                        $('#btn_overlay').hide();
                    } else {
                        $.post('/zaek/admin/dev/prepare_list.php', {zAjax:true, zAjaxType:'json'},function(d) {
                            try {
                                d = JSON.parse(d);
                                $('#version_light tbody tr').each(function() {
                                    var code = $(this).find('.code').text();
                                    $(this).find('.change_cnt').text(d[code]);
                                });
                                $('#btn_overlay').hide();
                            } catch ( e ) {
                                console.log(d);
                            }
                        });
                    }
                });
                return false;
            });
        </script>
    </div>
    <div class="row">
        <div class="col-lg-2">
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title">Теги <span id="file_path"></span></h3>
                </div>
                <div class="box-body fluid">
                    <ul class="full_width_list" id="tag_list_2" data-zlist-language="rus">
                        <?php
                        foreach ( $aModules as $module ) {
                            ?>
                            <li data-zlist-id="<?=$module?>" data-zlist-title="<?=$module?>"><a href="#"><?=$module?></a></li>
                            <?php
                        }
                        ?>
                        <li data-zlist-id="ignore" data-zlist-title="Игнорировать"><a href="#">Игнорировать</a></li>
                        <li data-zlist-id="empty" data-zlist-title="Игнорировать"><a href="#">Пустые</a></li>
                    </ul>
                </div>
                <script>
                    $(function() {
                        $('#tag_list_2').on('z_list:selected', function(e,el,params) {
                            $('#file_tree_by_tag').fileTree({
                                root: '/',
                                script : '/zaek/admin/dev/tag_tree.php?tag=' + el.id
                            }, function(file, type) {

                            });
                        });
                    });
                </script>
                <div class="overlay" id="tag2_overlay" style="display:none;"><i class="fa fa-spin fa-refresh"></i></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title">Файловая система</h3>
                </div>
                <div class="box-body">
                    <div id="file_tree_by_tag"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title">Файлы без тега </h3>
                    <div class="pull-right"><i class="fa fa-refresh" id="no_tag_refresh"></i></div>
                </div>
                <div class="box-body">
                    <div id="file_not_assigned"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title">Версии</h3>
                </div>
                <div class="box-body">
                    <table id="version_light" class="table">
                        <thead>
                        <tr>
                            <th>Название</th>
                            <th>Код</th>
                            <th>Дата создания</th>
                            <th>Количество изменений</th>
                            <th>Текущая версия</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ( $aModules as $module ) {
                            $v = exec('cd "/home/web/z/za-ek/rep_by_tag/'.$module.'" && git tag | tail -1');
                            ?>
                            <tr>
                                <td class="name"><?=$module?></td>
                                <td class="code"><?=$module?></td>
                                <td class="creation_time"></td>
                                <td class="change_cnt"></td>
                                <td class="current_version"><input type="text" value="<?=$v?>"/></td>
                                <td><button class="btn btn-danger">принять</button></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title">Обновление </h3>
                </div>
                <div class="box-body">
                    <label class="form-group">
                        Модуль
                        <input type="text" class="form-control" id="update_module"/>
                    </label>
                    <label class="form-group">
                        Версия
                        <input type="text" class="form-control" id="update_version"/>
                    </label>
                    <label class="form-group">
                        Цена
                        <input type="text" class="form-control" id="update_price"/>
                    </label>
                    <h4>Текст обновления</h4>
                    <textarea class="form-control" rows="8" id="update_comment"></textarea>
                    <div class="row">
                        <div class="col-lg-12 text-right"><button class="btn btn-primary" id="button_commit">Сохранить</button></div>
                    </div>
                    <h4>Обновление:</h4>
                    <div id="update_diff"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(function() {
        $('#no_tag_refresh').click(function() {
            $(this).addClass('fa-spin');
            $.post ( '/zaek/admin/dev/no_tag.php', {zAjax:true, zAjaxType:'json'},function(d) {
                d = JSON.parse(d);
                $('#file_not_assigned').html('');
                if ( d.length ) {
                    $(d).each(function(k,v) {
                        $('#file_not_assigned').append('<li>'+v+'</li>');
                    });
                }
                $('#no_tag_refresh').removeClass('fa-spin');
            });
            return false;
        });
        $('#file_tree').fileTree({
            root: '/',
            script : '/zaek/admin/dev/get_tree.php'
        }, function(file, type) {
            $('#tag_overlay').show();
            $('#file_path').text(file);
            $('#tag_list li.selected').removeClass('selected');
            $.post('/zaek/admin/dev/file_info.php', {zAjax:true, zAjaxType:'json', path:file},function(d) {
                $('#tag_overlay').hide();
                if ( d ) {
                    $('#tag_list li[data-zlist-id="'+d+'"]').click();
                }
            })
        });
        $('#version_light tbody tr button').click(function() {
            var tr = $(this).closest('tr');
            $('#update_diff').load('/zaek/admin/dev/show_change.php?module=' + tr.find('.code').text());
            $('#update_module').val(tr.find('.code').text());
            $('#update_version').val(tr.find('.current_version input').val());
            return false;
        });
        $('#button_commit').click(function() {
            var tr = $(this).closest('tr');
            var d = {
                code : $('#update_module').val(),
                version : $('#update_version').val(),
                msg : $('#update_comment').val(),
                price : $('#update_price').val()
            };
            console.log(d);
            if ( d.msg != null ) {
                $.post('/zaek/admin/dev/commit.php', d, function(e) {
                    alert(e);
                });
            }
            return false;
        });

    })
</script>
