@extends('manager::template.page')
@section('content')
    @push('scripts.top')
        <script>

          var actions = {
            save: function() {
              documentDirty = false;
              form_save = true;
              document.mutate.save.click();
              //saveWait('mutate');
            },
            duplicate: function() {
              if (confirm("{{ ManagerTheme::getLexicon('confirm_duplicate_record') }}") === true) {
                documentDirty = false;
                document.location.href = "index.php?id={{ $data->id }}&a=96";
              }
            },
            delete: function() {
              if (confirm("{{ ManagerTheme::getLexicon('confirm_delete_template') }}") === true) {
                documentDirty = false;
                document.location.href = 'index.php?id={{ $data->id }}&a=21';
              }
            },
            cancel: function() {
              documentDirty = false;
              document.location.href = 'index.php?a=76';
            }
          };

          document.addEventListener('DOMContentLoaded', function() {
            var h1help = document.querySelector('h1 > .help');
            h1help.onclick = function() {
              document.querySelector('.element-edit-message').classList.toggle('show');
            };
          });

        </script>
    @endpush

    <form name="mutate" method="post" action="index.php">
        {!! get_by_key($Events, 'OnTempFormPrerender') !!}

        <input type="hidden" name="a" value="20">
        <input type="hidden" name="id" value="{{ $data->id }}">
        <input type="hidden" name="mode" value="16">

        <h1>
            <i class="fa fa-newspaper-o"></i>
            {{ $data->templatename }}
            <small>({{ $data->id }})</small>
            <i class="fa fa-question-circle help"></i>
        </h1>

        @include('manager::partials.actionButtons', ['select' => '', 'save' => '', 'new' => '1', 'duplicate' => '', 'delete' => '', 'cancel' => ''])

        <div class="container element-edit-message">
            <div class="alert alert-info">{{ ManagerTheme::getLexicon('template_msg') }}</div>
        </div>

        <div class="tab-pane" id="templatesPane">
            <script>
              var tp = new WebFXTabPane(document.getElementById('templatesPane'), {{ get_by_key($modx->config, 'remember_last_tab') ? 1 : 0 }});
            </script>

            <div class="tab-page" id="tabTemplate">
                <h2 class="tab">{{ ManagerTheme::getLexicon('template_edit_tab') }}</h2>
                <script>tp.addTabPage(document.getElementById('tabTemplate'));</script>

                <div class="container container-body">
                    <div class="form-group">
                        @include('manager::form.row', [
                            'for' => 'templatename',
                            'label' => ManagerTheme::getLexicon('template_name'),
                            'small' => ($data->id == get_by_key($modx->config, 'default_template') ? '<b class="text-danger">' . mb_strtolower(rtrim(ManagerTheme::getLexicon('defaulttemplate_title'), ':'), ManagerTheme::getCharset()) . '</b>' : ''),
                            'element' => '<div class="form-control-name clearfix">' .
                                ManagerTheme::view('form.inputElement', [
                                    'name' => 'templatename',
                                    'value' => $data->templatename,
                                    'class' => 'form-control-lg',
                                    'attributes' => 'onchange="documentDirty=true;"'
                                ]) .
                                ($modx->hasPermission('save_role')
                                ? '<label class="custom-control" data-tooltip="' . ManagerTheme::getLexicon('lock_template') . "\n" . ManagerTheme::getLexicon('lock_template_msg') .'">' .
                                 ManagerTheme::view('form.inputElement', [
                                    'type' => 'checkbox',
                                    'name' => 'locked',
                                    'checked' => ($data->locked == 1)
                                 ]) .
                                 '<i class="fa fa-lock"></i>
                                 </label>
                                 </div>
                                 <small class="form-text text-danger hide" id="savingMessage"></small>
                                 <script>if (!document.getElementsByName(\'templatename\')[0].value) document.getElementsByName(\'templatename\')[0].focus();</script>'
                                : '')
                        ])

                        @include('manager::form.input', [
                            'name' => 'description',
                            'id' => 'description',
                            'label' => ManagerTheme::getLexicon('template_desc'),
                            'value' => $data->description,
                            'attributes' => 'onchange="documentDirty=true;" maxlength="255"'
                        ])

                        @include('manager::form.select', [
                            'name' => 'categoryid',
                            'id' => 'categoryid',
                            'label' => ManagerTheme::getLexicon('existing_category'),
                            'value' => $data->category,
                            'first' => [
                                'text' => ''
                            ],
                            'options' => $categories,
                            'attributes' => 'onchange="documentDirty=true;"'
                        ])

                        @include('manager::form.input', [
                            'name' => 'newcategory',
                            'id' => 'newcategory',
                            'label' => ManagerTheme::getLexicon('new_category'),
                            'value' => (isset($data->newcategory) ? $data->newcategory : ''),
                            'attributes' => 'onchange="documentDirty=true;" maxlength="45"'
                        ])

                    </div>

                    @if($modx->hasPermission('save_role'))
                        <div class="form-group">
                            <label>
                                @include('manager::form.inputElement', [
                                    'name' => 'selectable',
                                    'id' => 'selectable',
                                    'type' => 'checkbox',
                                    'checked' => ($data->selectable == 1),
                                    'attributes' => 'onchange="documentDirty=true;"'
                                ])
                                {{ ManagerTheme::getLexicon('template_selectable') }}
                            </label>
                        </div>
                    @endif
                </div>

                <!-- HTML text editor start -->
                <div class="navbar navbar-editor">
                    <span>{{ ManagerTheme::getLexicon('template_code') }}</span>
                </div>
                <div class="section-editor clearfix">
                    @include('manager::form.textareaElement', [
                        'name' => 'post',
                        'value' => (isset($data->post) ? $data->post : $data->content),
                        'class' => 'phptextarea',
                        'rows' => 20,
                        'attributes' => 'onChange="documentDirty=true;"'
                    ])
                </div>
                <!-- HTML text editor end -->

                <input type="submit" name="save" style="display:none">
            </div>

            <div class="tab-page" id="tabAssignedTVs">
                <h2 class="tab">{{ ManagerTheme::getLexicon('template_assignedtv_tab') }}</h2>
                <script>tp.addTabPage(document.getElementById('tabAssignedTVs'));</script>
                <input type="hidden" name="tvsDirty" id="tvsDirty" value="0">

                <div class="container container-body">
                    @if($tvs['total_selected'])
                        <p>{{ ManagerTheme::getLexicon('template_tv_msg') }}</p>
                    @endif

                    @if($modx->hasPermission('save_template') && $tvs['total_selected'] > 1 && $data->id)
                        <div class="form-group">
                            <a class="btn btn-primary" href="?a=117&id={{ $data->id }}">{{ ManagerTheme::getLexicon('template_tv_edit') }}</a>
                        </div>
                    @endif

                    @if($tvs['total_selected'])
                        <ul>
                            @foreach($tvs['selectedTvs'] as $row)
                                <li>
                                    <label>
                                        @include('manager::form.inputElement', [
                                            'type' => 'checkbox',
                                            'name' => 'assignedTv[]',
                                            'value' => $row['tvid'],
                                            'checked' => 1,
                                            'attributes' => 'onchange="documentDirty=true; document.getElementById(\'tvsDirty\').value = 1;"'
                                        ])
                                        {!! $row['tvname'] !!}
                                        <small>({{ $row['tvid'] }})</small>
                                        - {!! $row['tvcaption'] !!}
                                        @if(!empty($row['tvdescription']))
                                            <small>({!! $row['tvdescription'] !!})</small>
                                        @endif
                                    </label>
                                    @if(!empty($row['tvlocked']))
                                        <em>({{ ManagerTheme::getLexicon('locked') }})</em>
                                    @endif
                                    <a href="?id={{ $row['tvid'] }}&a=301&or=16&oid={{ $data->id }}">{{ ManagerTheme::getLexicon('edit') }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        {{ ManagerTheme::getLexicon('template_no_tv') }}
                    @endif

                    @if($tvs['total_unselected'])
                        <hr>
                        <p>{{ ManagerTheme::getLexicon('template_notassigned_tv') }}</p>
                        <ul>
                            @php($preCat = '')
                            @php($insideUl = 0)
                            @foreach($tvs['unselectedTvs'] as $row)
                                @if(isset($tvs['selectedTvs'][$row['tvid']]))
                                    @continue
                                @endif
                                @php($row['category'] = stripslashes($row['category']))
                                @if($preCat !== $row['category'])
                                    @if($insideUl)
                                        </ul>
                                    @endif
                                    <li>
                                        <strong>{{ $row['category'] }}
                                            @if($row['catid'] != '')
                                                <small>{{ $row['catid'] }}</small>
                                            @endif
                                        </strong>
                                        <ul>
                                @php($insideUl = 1)
                                @endif
                                        <li>
                                            <label>
                                                @include('manager::form.inputElement', [
                                                    'type' => 'checkbox',
                                                    'name' => 'assignedTv[]',
                                                    'value' => $row['tvid'],
                                                    'attributes' => 'onchange="documentDirty=true; document.getElementById(\'tvsDirty\').value = 1;"'
                                                ])
                                                {!! $row['tvname'] !!}
                                                <small>({{ $row['tvid'] }})</small>
                                                - {!! $row['tvcaption'] !!}
                                            </label>
                                            @if(!empty($row['tvlocked']))
                                                <em>({{ ManagerTheme::getLexicon('locked') }})</em>
                                            @endif
                                            <a href="?id={{ $row['tvid'] }}&a=301&or=16&oid={{ $data->id }}">{{ ManagerTheme::getLexicon('edit') }}</a>
                                        </li>
                                    </li>
                                @php($preCat = $row['category'])
                            @endforeach
                            @if($insideUl)
                                </ul>
                            @endif
                        </ul>
                    @endif
                </div>
            </div>

            {!! get_by_key($Events, 'OnTempFormRender') !!}
        </div>
    </form>
@endsection
