    @extends('layout')

    @section('content')
        <style>
            .card {
                margin-bottom: 20px;
                border: 1px solid rgba(0, 0, 0, 0.125);
                border-radius: 0.25rem;
            }

            .card-img {
                width: 100%;
                height: auto;
            }

            .card-title {
                font-size: 1.25rem;
                margin-bottom: 0.75rem;
            }

            .card-text {
                margin-bottom: 1rem;
            }

            .card-body {
                padding: 1.25rem;
            }

            .card-body p {
                margin-bottom: 0.5rem;
            }

            .btn {
                margin-right: 5px;
            }
        </style>
        <div class="container">
            <div class="row justify-content-center p-5">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header row"><b>Newsletters</b></div>

                        <div class="card-body">
                            <!-- Button to trigger modal -->
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#addNewsletterModal">
                                Add Newsletter
                            </button>
                        </div>

                        <div class="card-body row">
                            <div class="col-md-6">
                                <label for="category">Filter by Category:</label>
                                <select class="form-control" id="category">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="search">Search by Newsletter Name:</label>
                                <input type="text" class="form-control" id="search"
                                    placeholder="Enter newsletter name">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <!-- This is where the filtered newsletters will be displayed -->
                        <div id="filteredNewsletters"></div>

                        <!-- Existing newsletters displayed here -->
                        @foreach ($newsletters as $newsletter)
                            <!-- Each individual newsletter -->
                        @endforeach
                    </div>
                    @foreach ($newsletters as $newsletter)
                        <div class="card mb-3">
                            <img src="{{ asset('assets/images/' . $newsletter->images) }}" class="card-img-top w-25"
                                alt="Newsletter Image">
                            <div class="card-body">
                                <h5 class="card-title">{{ $newsletter->title }}</h5>
                                <p class="card-text">{!! $newsletter->content !!}</p>
                                <p><b>Categories:</b>
                                    @foreach ($newsletter->categories as $category)
                                        ,{{ $category->name }}
                                    @endforeach
                                </p>
                                <button type="button" class="btn btn-info" data-toggle="modal"
                                    data-target="#editNewsletterModal{{ $newsletter->id }}">Edit</button>
                                <button type="button" class="btn btn-danger" data-toggle="modal"
                                    data-target="#confirmDeleteModal{{ $newsletter->id }}">Delete</button>
                                <form action="{{ route('send_emails') }}" method="POST">
                                    @csrf
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-warning text-black mt-2">Send Newsletter
                                            Email</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @foreach ($newsletters as $newsletter)
            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="confirmDeleteModal{{ $newsletter->id }}" tabindex="-1" role="dialog"
                aria-labelledby="confirmDeleteModalLabel{{ $newsletter->id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmDeleteModalLabel{{ $newsletter->id }}">
                                Confirm Delete</h5>

                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete this newsletter?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <!-- Form to submit delete request -->
                            <form action="{{ route('newsletter.destroy', $newsletter->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <!-- Edit Newsletter Modal -->
            <div class="modal fade" id="editNewsletterModal{{ $newsletter->id }}" tabindex="-1" role="dialog"
                aria-labelledby="editNewsletterModalLabel{{ $newsletter->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editNewsletterModalLabel{{ $newsletter->id }}">Edit
                                Newsletter</h5>

                        </div>
                        <div class="modal-body">
                            <form action="{{ route('newsletter.update', $newsletter->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- Your form fields go here -->
                                <div class="form-group">
                                    <label for="image">Image</label>
                                    <input type="file" class="form-control-file" id="images" name="images">
                                </div>
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="{{ $newsletter->title }}">
                                </div>
                                <div class="form-group">
                                    <label for="content">Content</label>
                                    <textarea class="form-control" id="editor" name="content" rows="6">{{ $newsletter->content }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="user_id">User</label>
                                    <select class="form-control" id="user_id" name="user_id">
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ $newsletter->user_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="categories">Categories</label>
                                    <select class="form-control" id="categories" name="categories[]" multiple>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ in_array($category->id, $newsletter->categories->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
        </div>
        </div>
        </div>

        <!-- Add Newsletter Modal -->
        <div class="modal fade" id="addNewsletterModal" tabindex="-1" role="dialog"
            aria-labelledby="addNewsletterModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addNewsletterModalLabel">Add Newsletter</h5>

                    </div>
                    <form action="{{ url('newsletter') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="newsletterImage">Image</label>
                                <input type="file" class="form-control-file" id="images" name="images">
                            </div>
                            <div class="form-group">
                                <label for="newsletterTitle">Title</label>
                                <input type="text" class="form-control" id="newsletterTitle" name="title"
                                    placeholder="Enter title">
                            </div>
                            <div class="form-group">
                                <label for="editor">Content</label>
                                <!-- CKEditor WYSIWYG Editor -->
                                <textarea name="content" id="editorAddNewsletter"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="userId">User</label>
                                <select class="form-control" id="userId" name="user_id" disabled>
                                    <!-- Populate options with user IDs -->
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="categories">Categories</label>
                                <select class="form-control" id="categories" name="categories[]" multiple>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>

        <script>
            ClassicEditor
                .create(document.querySelector('#editor'))
                .then(editor => {
                    console.log(editor);
                })
                .catch(error => {
                    console.error(error);
                });
            ClassicEditor
                .create(document.querySelector('#editorAddNewsletter'))
                .then(editor => {
                    console.log(editor);
                })
                .catch(error => {
                    console.error(error);
                });
        </script>








        <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
        <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../assets/js/sidebarmenu.js"></script>
        <script src="../assets/js/app.min.js"></script>
        <script src="../assets/libs/simplebar/dist/simplebar.js"></script>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    @endsection
