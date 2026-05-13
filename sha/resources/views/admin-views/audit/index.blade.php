@extends('layouts.admin.app',['main_li'=>'Audit List','sub_li'=>''])
@section('title','Audit List')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Audit Sub Categories</h4>
                    <button class="btn btn-primary btn-round ms-auto" id="addAuditCategoryBtn" data-bs-toggle="modal" data-bs-target="#addAuditCategoryModal"><i class="fa fa-plus"></i> Add</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category Name</th>
                                <th>SubCategory Name</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ @$category->auditCategory->name ?? 'NA' }}</td>
                                <td>{{ @$category->auditSubCategory->name}}</td>
                                <td>{{ $category->name }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary btn-xs" onclick="editData('{{ $category->id }}')" title="Edit List"><i class="fa fa-edit"></i></a>
                                        <a class="btn action-btn btn--danger btn-outline-danger btn-xs" href="javascript:" onclick="form_alert('category-{{$category->id}}','Want to delete this audit List?')" title="Delete List"><i class="fa fa-trash"></i></a>
                                        <form action="{{ route('admin.audit-list.destroy', [$category->id]) }}" method="post" id="category-{{$category->id}}">
                                            @csrf @method('delete')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addAuditCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addAuditCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAuditCategoryModalLabel">Add/Edit Audit Sub  Category</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="javascript:" method="post" id="add_edit_form">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id">

                <div class="modal-body">
                    <div class="form-group">
                        <label for="license_id">Category</label>
                        <select class="form-control" name="category_id" id="category_id" onchange="getSubCategory();">
                            <option value="">Select Category</option>
                            @foreach($auditcategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="license_id">Sub Category</label>
                        <select class="form-control" name="sub_category_id" id="sub_category_id">
                            <option value="">Select</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Category Name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $("#basic-datatables").DataTable();
    });

    $("#addAuditCategoryBtn").on("click",function(){
        $("#name").val("");
        $("#category_id").val("").change();
        $("#sub_category_id").val("").change();
        $("#edit_id").val("");
    })
    $('#add_edit_form').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        let url, type;

        if ($("#edit_id").val() === '') {
            url = "{{ route('admin.audit-list.store') }}";
            type = "POST";
        } else {
            url = `{{ route('admin.audit-list.update', ':id') }}`.replace(':id', $("#edit_id").val());
            type = "POST";
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            type: type,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                $('#addAuditCategoryModal').modal('hide');
                successMessage(data.msg);
                setTimeout(function () {
                    window.location.reload();
                }, 2000);
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                errorMessage('An error occurred.');
            }
        });
    });

    function getSubCategory(categoryid = '') {
        var category_id = $('#category_id').val();
        if(category_id) {
            const url = `{{ route('admin.getauditsubcategory', ':id') }}`.replace(':id', category_id);

            $.get(url, function (data) {
                $('#sub_category_id').empty();
            
                // Add a default "Select" option
                $('#sub_category_id').append('<option value="">Select</option>');
                
                // Loop through the data and add each item as an option
                console.log(category_id);    
                $.each(data.data, function (index, item) {
                    if(categoryid == item.id) {
                        $('#sub_category_id').append(`<option value="${item.id}" selected>${item.name}</option>`);
                    } else {
                        $('#sub_category_id').append(`<option value="${item.id}">${item.name}</option>`);
                    }
                });
            });
        } else {
            errorMessage('Please select an ownership type.');
        }
    }

    function editData(id) {
        const url = `{{ route('admin.audit-list.show', ':id') }}`.replace(':id', id);

        $.get(url, function (data) {
            $("#name").val(data.data.name);
            $("#category_id").val(data.data.category_id);
            getSubCategory(data.data.sub_category_id)
            $("#edit_id").val(data.data.id);
            $("#addAuditCategoryModal").modal('show');
        });
    }
</script>
@endpush
