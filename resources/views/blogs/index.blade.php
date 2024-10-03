@extends('auth.layouts')

@section('content')

<div class="row justify-content-center mt-5">
    <div class="col-md-8">
        <div class="card-body">
            <div class="alert alert-success" id="message">
                @if ($message = Session::get('success'))
                    {{ $message }}
                @else
                    You are Blog View!
                @endif
            </div>
        </div>
        <div class="mb-3">
            <a href="{{ route('blog.create') }}" class="btn btn-success">Create New Blog</a>
        </div>
        <div class="mb-3">
            <label>
                <input type="checkbox" id="showMyBlogs" checked> Show Only My Blogs
            </label>
        </div>
        <div class="card">
            <div class="card-header">Blogs</div>
            <div class="card-body">
                <div class="row">
                    @foreach ($blogs as $blog)
                        <div class="col-md-4 mb-4" data-created-by="{{ $blog->createdBy }}">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $blog->name }}</h5>
                                    <p class="card-text">{{ substr($blog->detail, 0, 50) . (strlen($blog->detail) > 50 ? '...' : '') }}</p>
                                    @php
                                        $user = \App\Models\User::find($blog->createdBy);
                                    @endphp
                                    @if ($user)
                                        <p class="card-author" data-created-by="{{ $blog->createdBy }}">By {{ $user->name }}</p>
                                    @else
                                        <p class="card-author" data-created-by="{{ $blog->createdBy }}">By Former User</p>
                                    @endif
                                    @if($loggedUser->id===$blog->createdBy)
                                        <a href="{{ route('blog.show', $blog->id) }}" class="btn btn-primary">Show Blog</a>
                                    @elseif (in_array($blog->id, $allowedBlogIds))
                                        <p>You purchased this blog!</p>
                                        <a href="{{ route('blog.show', $blog->id) }}" class="btn btn-primary">Show Blog</a>
                                    @else
                                        <a href="{{ route('blog.buy', ['userId' => $loggedUser->id, 'blogId' => $blog->id]) }}" class="btn btn-primary">Buy Blog</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    if (typeof jQuery == 'undefined') {
        console.log("jQuery is not loaded.");
    } else {
        console.log("jQuery is loaded.");
    }

    $(document).ready(function() {
        console.log("Document is ready.");
        var user_id = "{{ Auth::user()->id }}"; // Get the user's ID

        $('.col-md-4').each(function() {
            var createdBy = $(this).data('created-by');

            if (createdBy != user_id) {
                $(this).hide(); // Hide blogs not created by the user
            }
        });

        $('#showMyBlogs').on('change', function() {
            var showOnlyMyBlogs = $(this).is(':checked');

            $('.col-md-4').each(function() {
                var createdBy = $(this).data('created-by');

                if (showOnlyMyBlogs) {
                    if (createdBy != user_id) {
                        $(this).hide();
                    }
                } else {
                    $(this).show();
                }
            });
        });

        setTimeout(function() {
            $('#message').fadeOut('fast', function() {
                $(this).remove();
            });
        }, 1500);
    });
</script>
