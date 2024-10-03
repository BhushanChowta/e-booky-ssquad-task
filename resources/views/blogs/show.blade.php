@extends('auth.layouts')

@section('content')
    <br>
    <div class="row">  
        <div class="col-lg-12 margin-tb">  
            <div class="pull-left">  
                <h2> Show Blog</h2>  
            </div>  
            <div class="pull-right">  
                @if($loggedUser->is_admin or $loggedUser->id===$blog->createdBy)
                    <a class="btn btn-warning" href="{{ route('blog.edit', $blog->id) }}"> Edit</a>
                    <form action="{{ route('blog.destroy', $blog->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger delete-blog">Delete</button>
                   </form>
                @endif
                <a class="btn btn-primary back-button" href="{{url()->previous()}}"> Back</a>  
            </div>  
        </div>  
    </div>  
    <br>

    <div class="row">  
        <div class="col-xs-12 col-sm-12 col-md-12">  
            <div class="form-group">  
                <h4>{{ $blog->name }}  </h4>
            </div>  
        </div>  
        <div class="col-xs-12 col-sm-12 col-md-12">  
            <div class="blog-details">
                <div class="form-group">
                    <p style="font-size: 18px; line-height: 1.6; color: #666;">{{ $blog->detail }}</p>
                </div>
            </div>
        </div>  
        <div class="col-xs-12 col-sm-12 col-md-12 top-right">  
            <div class="form-group">  
                @php
                    $user = \App\Models\User::find($blog->createdBy);
                @endphp
                @if (!$user)
                    <div class="top-right-content">
                        <div class="created-by">
                            <strong>Created By:</strong> Former User
                        </div>
                    </div>
                @else
                    <div class="top-right-content">
                        <div class="created-by">
                            <strong>Created By:</strong> {{ $createdUser->name }}
                        </div>
                        <div class="profile-picture-container">
                            <img src="{{ asset('storage/' .$createdUser->profile_picture) }}" alt="{{ $createdUser->name }}" class="profile-picture rounded-circle" style="width: 100px; height: 100px;">
                        </div>
                    </div>
                @endif
            </div>  
        </div>  
    </div>  

    <script>
        $(document).ready(function() {
            $('.delete-blog').click(function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this blog?')) {
                    $(this).closest('form').submit();
                }
            });
        });
    </script>

    <style>
        .top-right {
            position: relative;
            text-align: right;
        }

        .top-right-content {
            position: absolute;
            top: 0;
            right: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .created-by {
            margin-bottom: 5px;
        }

        .profile-picture {
            max-width: 100px;
            max-height: 100px;
            /* border-radius: 50%; */
        }
        .back-button {
            float: right;
        }
    </style>
@endsection
