@extends('auth.layouts')

@section('content')
    <br>
    <div class="row">  
        <div class="col-lg-12 margin-tb">  
            <div class="pull-left">  
                <h2>Add New Blog</h2>  
            </div>  
            <div class="pull-right">  
                <a class="btn btn-primary" href="{{ url()->previous() }}"> Back</a>  
            </div>  
        </div>   
    </div>  
    <br>
  
    @if ($errors->any())  
        <div class="alert alert-danger">  
            <strong>Whoops!</strong> Your entered input has some problems. <br><br>  
            <ul>  
                @foreach ($errors->all() as $error)  
                    <li>{{ $error }}</li>  
                @endforeach  
            </ul>  
        </div>  
    @endif  
  
    <form id="blogForm" action="{{ route('blog.store') }}" method="POST">  
        @csrf  
  
        <div class="row">  
            <div class="col-xs-12 col-sm-12 col-md-12">  
                <div class="form-group">  
                    <strong>Name:</strong>  
                    <input type="text" name="name" class="form-control" placeholder="Name">  
                </div>  
            </div>  
            <div class="col-xs-12 col-sm-12 col-md-12">              
                <br> 
                <div class="form-group"> 
                    <strong>Detail:</strong>  
                    <textarea class="form-control" style="height:150px" name="detail" placeholder="Detail"></textarea>  
                </div>  
            </div>  
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">              
                <br>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>  
        </div>  
  
    </form>  
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include jQuery Validation plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#blogForm').on('submit', function (e) {
                e.preventDefault(); 
                if ($('#blogForm').valid()) {
                    // If validation passes, submit the form
                    this.submit();
                }
            });

            $('#blogForm').validate({
                rules: {
                    name: {
                        required: true
                        // ,maxlength: 255
                    },
                    detail: {
                        required: true
                    }
                },
                messages: {
                    name: {
                        required: "Please enter a name"
                        // ,maxlength: "Name cannot exceed 255 characters"
                    },
                    detail: {
                        required: "Please enter the Details"
                    }
                },
                errorElement: "div", // Wrap error messages in a <div> tag
                errorPlacement: function (error, element) {
                    error.insertAfter(element);
                }
            });
        });
    </script>
@endsection  
