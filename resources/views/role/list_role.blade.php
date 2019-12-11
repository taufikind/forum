@extends('layouts.app')
@section('title',"user: $name")
@section('content')
<div class="container">
    <div class="jumbotron" id="tc_jumbotron_profile">
      <div class="card-body">
        <div class="text-center">
         <div class="profile_img">
           <img src="{{asset('images/profile.png')}}" style="background: #fff;">
         </div>
          <div id="user_name">
            <h3>{{$name}}</h3>
            <br>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="card" style="border: none; text-align: center;">
            <div class="card-header" style="background: #f5f8fa; padding: 0;">
              <div class="footer_sosial_profile">
              <a href="">  <i class="fa fa-edit"></i></a>
              <a href=""><i class="fa fa-list"></i></a>
              <a href=""><i class="fa fa-search"></i></a>
              <a href=""><i class="fa fa-facebook"></i></a>
              <a href=""><i class="fa fa-twitter"></i></a>
              <a href=""><i class="fa fa-instagram"></i></a>
              <a href=""><i class="fa fa-google-plus"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12" id="tc_container_wrap">
           <div class="card" id="tc_paneldefault">
              <div class="card-body" id="tc_panelbody"  style="background: #f9f9f9;">
                 <div class="card">
                    <div class="card-header" style="background-color: #2ab27b;">
                      <div class="menu_a" style="float: left;">
                      <a href="#" id="create_role">Add Role</a>
                      </div>
                 </div>
              </div>
          <div class="row">
           <div class="col-md-12">
            <table id="role_table" class="table table-bordered">
                <thead>
                  <tr>
                   <th>Id</th>
                   <th>No.</th>
                   <th>Role Name</th>
                   <th>Role Status</th>
                   <th>created_at</th>
                   <th>updated_at</th>
                   <th>Action</th>
                </tr>
                </thead>
                  </table>
                   </div>
                   </div>
                    <div class="card" style="border: none;">
                    <div class="card-header"></div>
                    <div class="card-body" style="background: rgb(90, 90, 90)"></div>
                     <div class="card-header"></div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Modal -->
<div class="modal fade" id="modal_crud" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="judulmodal"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" id="add_role_form"  name="add_role_form" >
          <input type="hidden" name="role_id" id="role_id">
          <div class="position-relative  form-group">
              <legend for="role_name" class="col-sm-2 col-form-label">Role Name</legend>
              <div class="col-sm-10">
                  <input name="role_name" id="role_name" type="text" class="form-control">
              </div>
          </div>
          <fieldset class="position-relative  form-group">
                <legend class="col-form-label col-sm-2" >Status</legend>
                <div class="col-sm-10">
                    <div class="position-relative form-check">
                        <label class="form-check-label">
                        <input name="role_status" id="role_status1" type="radio" class="form-check-input" value="1"> Aktif</label>
                    </div>
                    <div class="position-relative form-check">
                        <label class="form-check-label">
                        <input name="role_status" id="role_status2" type="radio" class="form-check-input" value="0"> Tidak Aktif</label>
                    </div>
                </div>
                <div class="invalid-feedback">
                    You must agree before submitting.
                </div>
            </fieldset>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button class="btn btn-primary" type="submit" id="btn_save_role">Save</button>
        </div>
        </form>
      </div>
    </div>
  </div>



@push('scripts')
<script>
$(document).ready( function () {
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
$('#role_table').DataTable({
         processing: true,
         serverSide: true,
         ajax: {
          url: "{{ route('list_role') }}",
          type: 'GET',
         },
         columns: [
                  {data: 'role_id', name: 'role_id', 'visible': false},
                  {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false,searchable: false},
                  { data: 'role_name', name: 'role_name' },
                  { data: 'role_status', name: 'role_status' },
                  { data: 'created_at', name: 'created_at' },
                  { data: 'updated_at', name: 'updated_at' },
                  {data: 'action', name: 'action', orderable: false},
               ],
        order: [[0, 'desc']]
      });

      $('#create_role').click(function () {
              $('#btn_save_role').val("create-role");
              $('#role_id').val('');
              $('#judulmodal').html("Add New Role");
              $('#add_role_form').trigger("reset");
              $('#modal_crud').modal('show');
          });

          $(document).on("click","#btn_save_role",function(e) {
                  e.preventDefault();
                           var form = $('#add_role_form')[0];
                           var postData = new FormData(form);
                           $.ajax({
                               // data: $('#add_candidate_form').serialize(),
                               url: "{{ route('add_role') }}",
                               type: "POST",
                               // dataType: 'json',
                               data : postData,
                               cache : false,
                               contentType : false,
                               processData : false,
                               success: function (data) {
                                   $('#add_role_form').trigger("reset");
                                   $('#modal_crud').modal('hide');
                                   $('#btn_save_role').html('Save');
                                   var oTable = $('#role_table').dataTable();
                                   oTable.fnDraw(false);
                                   Swal.fire({
                                                     title:'SUCCESS!',
                                                     text: 'Bank has been Succeed!',
                                                     type: 'success'
                                                   });
                               },
                               error: function (data) {
                                   console.log('Error:', data);
                                   $('#btn_save_role').html('Save');
                               }
                           });
                         });

                         $('body').on('click', '#delete-role', function () {

              var role_id = $(this).data("id");
              // confirm("Are You sure want to delete !");
              Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
              }).then((result) => {
                if (result.value) {
                  $.ajax({
                      type: "get",
                      url: "{{url('delete_role')}}"+ '/' + role_id,
                      success: function (data) {
                        var oTable = $('#role_table').dataTable();
                        oTable.fnDraw(false);
                      },
                      error: function (data) {
                          console.log('Error:', data);
                      }
                  });
                  Swal.fire(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                  )
                }
              });
          });

          $('body').on('click', '.edit-role', function () {
              var role_id = $(this).data('id');
               $.get("{{url('edit_role')}}"+ '/' + role_id, function (data) {
                  $('#judulmodal').html("Edit Role");
                   $('#btn_save_role').val("edit-role");
                   $('#modal_crud').modal('show');
                   $('#role_id').val(data.role_id);
                   $('#role_name').val(data.role_name);
                   if (data.role_status == 1) {
                     $("#role_status1").prop('checked', true);
                   }else {
                     $("#role_status2").prop('checked', true);
                   }
               })
            });



        });





</script>
@endpush
