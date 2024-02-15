@extends('layouts.admin')
@section('title','All Para Masters')
@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">All Para Masters</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                @if($access->edit == '1')
                                    <a href="{{ route('admin.addParaMaster') }}" class="headerButtonStyle" role="button" title="Add Para Master">
                                        Add Para Master
                                    </a>
                                @endif
                            </li>
                        </ol>
                    </div>
                    
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <table id="tableList" class="table table-striped table-bordered nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sr. No</th>
                                    <th>Para Code</th>
                                    <th>Para Description</th>
                                    @if($access->delete != '')
                                        <th>Status</th>
                                    @endif
                                    @if($access->delete == '1')
                                        <th class='notexport'>Actions</th>
                                    @else
                                        @if($access->add != '' || $access->edit != '' || $access->delete != '')
                                            <th class='notexport'>Actions</th>
                                        @endif
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if(!is_null($paras))
                                    @php
                                        $count = (($offset == 0) ? 1 : $offset+1); 
                                    @endphp
                                    @foreach($paras as $pk => $pv)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $pv->para_code }}</td>
                                            <td>{{ $pv->para_description }}</td>

                                            @if($access->delete != '')
                                                @php $checked = ''; @endphp
                                                @if($pv->is_active == 1) @php $checked = 'checked' @endphp @endif
                                                <td>
                                                    <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                                        <input class="form-check-input paraMasterStatus" type="checkbox" id="customSwitch{{ $pk }}" value="1" data-id="{{ $pv->id }}" {{ $checked }}>
                                                        <label class="form-check-label" for="customSwitch{{ $pk }}"></label>
                                                    </div>
                                                </td>
                                            @endif

                                            @if($admin == 'yes' || ($access->edit != '' && $access->delete != ''))
                                                <td>

                                                    <a class="btn btn-primary btn-sm waves-effect waves-light" href="{{ route('admin.paraCodeMasterList',base64_encode($pv->id)) }}" role="button" title="Para Code Master">
                                                        <i class="bx bxs-folder"></i>
                                                    </a>

                                                    <a class="btn btn-primary btn-sm waves-effect waves-light" href="{{ route('admin.editParaMaster',base64_encode($pv->id)) }}" role="button" title="Edit">
                                                        <i class="bx bx-edit-alt"></i>
                                                    </a>
                                                    
                                                    <a class="btn btn-danger btn-sm waves-effect waves-light" href="{{ route('admin.deleteParaMaster',base64_encode($pv->id)) }}" role="button" onclick="return confirm('Do you want to delete this para master?');" title="Delete">
                                                        <i class="bx bx-trash"></i>
                                                    </a>
                                                </td>
                                            @else
                                                @if($access->add != '')
                                                    <td>
                                                        <a class="btn btn-primary btn-sm waves-effect waves-light" href="{{ route('admin.paraCodeMasterList',base64_encode($pv->id)) }}" role="button" title="Para Code Master">
                                                            <i class="bx bxs-folder"></i>
                                                        </a>
                                                    </td>
                                                @endif
                                                @if($access->edit != '')
                                                    <td>
                                                        <a class="btn btn-primary btn-sm waves-effect waves-light" href="{{ route('admin.editParaMaster',base64_encode($pv->id)) }}" role="button" title="Edit">
                                                            <i class="bx bx-edit-alt"></i>
                                                        </a>
                                                    </td>
                                                @endif
                                                @if($access->delete != '')
                                                    <td>
                                                        <a class="btn btn-danger btn-sm waves-effect waves-light" href="{{ route('admin.deleteParaMaster',base64_encode($pv->id)) }}" role="button" onclick="return confirm('Do you want to delete this para master?');" title="Delete">
                                                            <i class="bx bx-trash"></i>
                                                        </a>
                                                    </td>
                                                @endif
                                            @endif

                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <div class="mt-2">
                            Showing {{ (($page - 1) * $perPage) + 1 }} to {{ min($page * $perPage, $recordCount) }} of {{ $recordCount }} entries
                        </div>
                        <div style="float:right;">
                            @if ($pageCount >= 1)
                                <nav aria-label="...">
                                    <ul class="pagination">
                                        <li class="page-item {{ ($page == 1) ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ route('admin.paraMasterList', ['page' => base64_encode(1)]) }}">First</a>
                                        </li>
                                        <li class="page-item {{ ($page == 1) ? 'disabled' : '' }}">
                                            <a class="page-link h5" href="{{ route('admin.paraMasterList', ['page' => base64_encode($page - 1)]) }}">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                        @for ($i = max(1, $page - 2); $i <= min($page + 4, $pageCount); $i++)
                                            <li class="page-item {{($page == $i) ? 'active' : '' }}" aria-current="page">
                                                <a class="page-link" href="{{ route('admin.paraMasterList', ['page' => base64_encode($i)]) }}">{{ $i }}</a>
                                            </li>
                                        @endfor
                                        <li class="page-item {{ ($page == $pageCount) ? 'disabled' : '' }}">
                                            <a class="page-link h5" href="{{ route('admin.paraMasterList', ['page' => base64_encode($page + 1)]) }}">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                        <li class="page-item {{ ($page == $pageCount) ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ route('admin.paraMasterList', ['page' => base64_encode($pageCount)]) }}">Last</a>
                                        </li>
                                    </ul>
                                </nav>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection