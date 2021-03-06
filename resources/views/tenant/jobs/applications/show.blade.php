@extends('tenant.layouts.default')

@section('tenant.content')
    <header class="job-applications-header py-4">
        <div class="row">
            <div class="col-sm-12">
                <div class="nav justify-content-end mb-1">
                    <!-- Job applications link -->
                    <a class="nav-item nav-link"
                       href="{{ route('tenant.jobs.applications.index', $job) }}">
                        View other applicants
                    </a>
                </div><!-- /.nav -->

                <div class="align-content-center">
                    <div class="my-2">
                        <img src="{{ $jobApplication->user->avatar ?: asset('img/avatars/default_avatar.png') }}"
                             alt="{{ $jobApplication->user->name }} avatar"
                             class="rounded-circle mx-auto d-block">
                    </div>
                    <h3 class="text-center">
                        {{ $jobApplication->user->name }}
                    </h3>
                    <p class="text-center">
                        @foreach($jobApplication->user->skills as $skillset)
                            <span class="badge badge-dark py-2 px-2 h4 border-radius-0 mx-1">
                                    {{ $skillset->skill->name }}
                                </span>
                        @endforeach
                    </p>
                </div>
            </div>
        </div>
    </header>
    <section class="job-portfolio-content py-4">
        <div class="row">
            <div class="col-sm-8">
                <section class="mb-4">
                    <h4>{{ $job->title }}</h4>

                    <article>
                        <div class="d-flex justify-content-between align-content-center">
                            <div>
                                <p>
                                    <strong>Posted</strong> {{ $job->published_at->diffForHumans() }}
                                </p>

                                <p>
                                    <strong>Salary: {{ !$job->salaryIsConfidential ? $job->currency : null }}</strong>

                                    @if($job->salaryIsConfidential)
                                        Confidential
                                    @elseif($job->salary_min == $job->salary_max)
                                        {{ $job->salary_min }}
                                    @else
                                        {{ $job->salary_min }} - {{ $job->salary_max }}
                                    @endif
                                </p>
                            </div>

                            <aside>
                                <!-- Deadline -->
                                <p>
                                    <strong>Deadline</strong> {{ $job->closed_at->diffForHumans() }}
                                </p>

                                <!-- View job link -->
                                <p>
                                    <a href="{{ route('jobs.show', $job) }}">View Job</a>
                                </p>
                            </aside>
                        </div>

                        <div>
                            <section class="collapse" id="collapseJobSummary">
                                <p>{{ $job->overview }}</p>

                                <p>
                                    <strong>Work site:</strong>
                                    {{ $job->on_location == false ? 'Remote / Off site' : 'On site' }}
                                </p>

                                <p>
                                    <strong>Type:</strong> {{ $job->type == 'full-time' ? 'Full time' : 'Part time' }}
                                </p>
                            </section>

                            <p>
                                <a id="toggleJobSummary" data-toggle="collapse" href="#collapseJobSummary" role="button"
                                   aria-expanded="false" aria-controls="collapseJobSummary">
                                    Show job summary...
                                </a>
                            </p>
                        </div>
                    </article>
                </section>

                <section class="my-2">
                    <h4>Portfolio</h4>

                    <div class="my-2 py-2">
                        <p class="lead">
                            <a href="{{ route('portfolio.index', $jobApplication->user->username) }}"
                               class="btn btn-primary">
                                Go here
                            </a> to view <strong>{{ $jobApplication->user->name }}'s</strong> full portfolio.
                        </p>
                    </div>
                </section>

                <!-- User's bid -->
                <section class="my-2 py-2">
                    <h4>User's bid</h4>

                    {{ $jobApplication->details }}
                </section>

                <!-- Rating, reviews and admin options -->
                <section class="job-portfolio-footer py-4">
                    <!-- todo: Show review & rating + accept or reject status -->

                    @if($jobApplication->accepted_at)
                        @if($jobApplication->declined_at)
                            <p class="lead">
                                Sorry, applicant has declined this job opportunity.
                            </p>
                        @else
                            <p class="lead">Congratulations! For accepting an applicant.</p>

                            <div class="d-flex justify-content-between align-content-end my-1">
                                <aside>
                                    <p>Found a more preferable candidate?</p>

                                    <a href="{{ route('tenant.jobs.applications.accept.update', [$job, $jobApplication]) }}"
                                       class="btn btn-danger"
                                       onclick="event.preventDefault(); document.getElementById('reject-accepted-applicant-form').submit()">
                                        Reject applicant
                                    </a>
                                </aside>
                                <div>
                                    <p>Or still want to look at more candidates?</p>
                                    <a href="{{ route('tenant.jobs.applications.accept.destroy', [$job, $jobApplication]) }}"
                                       class="btn btn-success"
                                       onclick="event.preventDefault(); document.getElementById('reconsider-accepted-applicant-form').submit()">
                                        Reconsider applicant
                                    </a>
                                </div>
                            </div>

                            <!-- Reject accepted applicant -->
                            <form method="POST"
                                  action="{{ route('tenant.jobs.applications.accept.update', [$job, $jobApplication]) }}"
                                  id="reject-accepted-applicant-form" style="display: none">
                                @csrf
                                @method('PUT')
                            </form>

                            <!-- Reconsider accepted applicant -->
                            <form method="POST"
                                  action="{{ route('tenant.jobs.applications.accept.destroy', [$job, $jobApplication]) }}"
                                  id="reconsider-accepted-applicant-form" style="display: none">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    @elseif($jobApplication->rejected_at)
                        <p class="lead">You have rejected this applicant.</p>

                        <div class="d-flex justify-content-between align-content-end my-1">
                            <aside>
                                <p>Changed your mind?</p>
                                <a href="{{ route('tenant.jobs.applications.reject.update', [$job, $jobApplication]) }}"
                                   class="btn btn-primary"
                                   onclick="event.preventDefault(); document.getElementById('accept-rejected-applicant-form').submit()">
                                    Accept applicant
                                </a>
                            </aside>

                            <div>
                                <p>Want to leave applicant for further consideration?</p>

                                <a href="{{ route('tenant.jobs.applications.reject.destroy', [$job, $jobApplication]) }}"
                                   class="btn btn-success"
                                   onclick="event.preventDefault(); document.getElementById('reconsider-rejected-applicant-form').submit()">
                                    Reconsider applicant
                                </a>
                            </div>
                        </div>

                        <!-- Accept rejected applicant -->
                        <form method="POST"
                              action="{{ route('tenant.jobs.applications.reject.update', [$job, $jobApplication]) }}"
                              id="accept-rejected-applicant-form" style="display: none">
                            @csrf
                            @method('PUT')
                        </form>

                        <!-- Reconsider rejected applicant -->
                        <form method="POST"
                              action="{{ route('tenant.jobs.applications.reject.destroy', [$job, $jobApplication]) }}"
                              id="reconsider-rejected-applicant-form" style="display: none">
                            @csrf
                            @method('DELETE')
                        </form>
                    @elseif($jobApplication->cancelled_at)
                        <p class="lead">
                            This applicant has cancelled their bid.
                        </p>
                    @else
                        <p class="lead">
                            Please review this application and send feedback to applicant.
                        </p>

                        <div class="d-flex justify-content-between align-content-end my-1">
                            <a href="{{ route('tenant.jobs.applications.reject.store', [$job, $jobApplication]) }}"
                               class="btn btn-danger"
                               onclick="event.preventDefault(); document.getElementById('reject-applicant-form').submit()">
                                Reject applicant
                            </a>
                            <a href="{{ route('tenant.jobs.applications.accept.store', [$job, $jobApplication]) }}"
                               class="btn btn-primary"
                               onclick="event.preventDefault(); document.getElementById('accept-applicant-form').submit()">
                                Accept applicant
                            </a>
                        </div>

                        <!-- Accept applicant -->
                        <form method="POST"
                              action="{{ route('tenant.jobs.applications.accept.store', [$job, $jobApplication]) }}"
                              id="accept-applicant-form" style="display: none">
                            @csrf
                        </form>

                        <!-- Reject applicant -->
                        <form method="POST"
                              action="{{ route('tenant.jobs.applications.reject.store', [$job, $jobApplication]) }}"
                              id="reject-applicant-form" style="display: none">
                            @csrf
                        </form>
                    @endif
                </section>
            </div>
            <div class="col-sm-4">
                @if($jobApplication->submitted_at)
                    <p><strong>Submitted</strong> {{ $jobApplication->submitted_at->diffForHumans() }}</p>
                @else
                    <p>Application not yet submitted.</p>
                @endif

                <div class="list-group mb-3">
                    <div class="list-group-item">
                        <p class="h4 my-0">CV / Resume</p>
                    </div>
                    <a href="{{ route('jobs.applications.cv.show', [$job, $jobApplication]) }}"
                       class="list-group-item list-group-item-action text-primary">
                        View user's CV
                    </a>
                </div>

                <div class="list-group mb-3">
                    <div class="list-group-item">
                        <p class="h4 my-0">Education</p>
                    </div>
                    @foreach($jobApplication->user->schools as $school)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-content-center">
                                <div>
                                    <p class="h5">{{ $school->name }}</p>
                                    <p>{{ $school->course }}</p>
                                    <p>{{ $school->education->name }}</p>
                                </div>
                                <aside>
                                    <time>{{ $school->enrollmentYear }}</time>
                                    <span> &ndash; </span>
                                    <time>{{ $school->graduationYear }}</time>
                                </aside>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="list-group mb-3">
                    <div class="list-group-item">
                        <p class="h4 my-0">Other attachments</p>
                    </div>
                    <div class="list-group-item">
                        No extra attachements found.
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $('#collapseJobSummary').on('hidden.bs.collapse', function () {
            $('#toggleJobSummary').text('Show job summary...')
        })

        $('#collapseJobSummary').on('shown.bs.collapse', function () {
            $('#toggleJobSummary').text('Hide job summary')
        })
    </script>
@endsection