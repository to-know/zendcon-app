@extends('layouts.app')

@section('content')
	<div id="silverscreen-app" class="container" v-cloak>

		<!-- Add Movie Form -->
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel panel-primary">
					<div class="panel-heading">Add Movie</div>

					<div class="panel-body">
						<form-errors :form="addMovieForm"></form-errors>

						<form class="form-horizontal">
							<div class="form-group">
								<label class="col-md-3 control-label">Name</label>

								<div class="col-md-6">
									<input type="text" class="form-control" name="movie" v-model="addMovieForm.movie">
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-6 col-md-offset-3">
									<button type="submit" class="btn btn-primary"
										@click.prevent="addMovie"
										:disabled="addMovieForm.adding">

										<span v-if="addMovieForm.adding">
											<i class="fa fa-btn fa-spinner fa-spin"></i>Adding
										</span>

										<span v-else>
											<i class="fa fa-btn fa-plus"></i>Add Movie
										</span>
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- Movie Listing -->
		<div v-if="movies.length > 0">
			<div class="row" v-for="movie in sortedMovies">
				<div class="col-md-8 col-md-offset-2">
					<div class="panel panel-default">
						<div class="panel-heading">
							<div class="pull-left" style="padding-top: 6px;">
								@{{ movie.name }}
							</div>

							<div class="pull-right">
								<button class="btn btn-danger" style="font-size: 18px; margin-right: 10px;"
									v-if="movie.user.id == currentUserId"
									@click="deleteMovie(movie)">

									<i class="fa fa-times"></i>
								</button>

								<button class="btn btn-success" style="font-size: 18px;"
									@click.prevent="voteForMovie(movie)"
									:disabled="movie.voting">

									<span v-if="movie.voting">
										<i class="fa fa-btn fa-spinner fa-spin"></i>Voting
									</span>

									<span v-else>
										<i class="fa fa-btn fa-thumbs-up"></i>Vote
									</span>
								</button>
							</div>

							<div class="clearfix"></div>
						</div>

						<div class="panel-body">
							<div class="pull-left">
								<strong><em>Submitted By:</em></strong> @{{ movie.user.name }}
							</div>

							<div class="pull-right">
								<div class="vote-count">
									+ @{{ movie.votes }}
								</div>
							</div>

							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
