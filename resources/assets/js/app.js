
// Core Application Dependencies...
require('./core/dependencies')

// Flatten errors and set them on the given form
var setErrorsOnForm = function (form, errors) {
    if (typeof errors === 'object') {
        form.errors = _.flatten(_.toArray(errors));
    } else {
        form.errors.push('Something went wrong. Please try again.');
    }
};

// Errors Component...
Vue.component('form-errors', {
	props: ['form'],

	template: `
	<div class="alert alert-danger" v-if="form.errors.length > 0">
		<strong>Whoops!</strong> Looks like something went wrong!

		<br><br>

		<ul>
			<li v-for="error in form.errors">
				{{ error }}
			</li>
		</ul>
	</div>
	`
})

// Vue Application...
var app = new Vue({
	el: '#silverscreen-app',


	data: {
		currentUserId: Zendcon.userId,
		pusher: null,
		pusherChannel: null,

		movies: [],

		addMovieForm: {
			movie: '',
			errors: [],
			adding: false
		},

		votingForm: {
			voting: false
		}
	},


	ready: function () {
		this.getAllMovies();

		this.pusher = new Pusher(Zendcon.pusherKey);
		this.pusherChannel = this.pusher.subscribe('movies');

		this.registerWebsocketEvents();
	},


	computed: {
		sortedMovies: function () {
			return _.sortBy(this.movies, function (m) {
				return m.votes;
			}).reverse();
		}
	},


	methods: {
		getAllMovies: function () {
			this.$http.get('/movies')
				.success(function (movies) {
					movies = _.each(movies, function (m) {
						m.voting = false;
					});

					this.movies = movies;
				});
		},


		addMovie: function () {
			this.addMovieForm.errors = [];
			this.addMovieForm.adding = true;

			this.$http.post('/movies', this.addMovieForm)
				.success(function (movie) {
					this.addMovieForm.movie = '';
					this.addMovieForm.adding = false;
				})
				.error(function (errors) {
					setErrorsOnForm(this.addMovieForm, errors);
					this.addMovieForm.adding = false;
				});
		},


		voteForMovie: function (movie) {
			movie.voting = true;

			this.$http.put('/movies/' + movie.id + '/vote')
				.success(function () {
					movie.voting = false;
				});
		},


		deleteMovie: function (movie) {
			this.movies = _.reject(this.movies, function (m) {
				return m.id === movie.id;
			});

			this.$http.delete('/movies/' + movie.id);
		},


		registerWebsocketEvents: function () {
			this.pusherChannel.bind('App\\Events\\MovieCreated', (message) => {
				message.movie.voting = false;
				this.movies.push(message.movie);
			});

			this.pusherChannel.bind('App\\Events\\MovieReceivedVote', (message) => {
				var movie = _.find(this.movies, function (m) {
					return m.id == message.movie.id;
				});

				if (movie !== undefined) {
					movie.votes = message.movie.votes;
				}
			});

			this.pusherChannel.bind('App\\Events\\MovieDeleted', (message) => {
				this.movies = _.reject(this.movies, function (m) {
					return m.id === message.id;
				});
			});
		}
	}
})
