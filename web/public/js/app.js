const STATUS_SUCCESS = 'success';
const STATUS_ERROR = 'error';
var app = new Vue({
	el: '#app',
	data: {
		login: '',
		pass: '',
		post: false,
		invalidLogin: false,
		invalidPass: false,
		invalidSum: false,
		posts: [],
		addSum: 0,
		amount: 0,
		likes: 0,
		commentText: '',
		boosterpacks: [],
		replyId:null,
		replyText:'',
	},
	computed: {
		test: function () {
			let data = [];
			return data;
		}
	},
	created(){
		let self = this
		self.get_all_posts()
		self.get_boosterpacks()
	},
	methods: {
		get_all_posts: function () {
			let self= this;
			axios
				.get('/main_page/get_all_posts')
				.then(function (response) {
					self.posts = response.data.posts;
				})
		},
		get_boosterpacks: function () {
			let self= this;
			axios
				.get('/main_page/get_boosterpacks')
				.then(function (response) {
					self.boosterpacks = response.data.boosterpacks;
				})
		},
 		logout: function () {
			axios.post('/main_page/logout')
				.then(function () {
					console.log ('logout');
					location.reload();
				})
		},
		logIn: function () {
			var self= this;
			if(self.login === ''){
				self.invalidLogin = true
			}
			else if(self.pass === ''){
				self.invalidLogin = false
				self.invalidPass = true
			}
			else{
				self.invalidLogin = false
				self.invalidPass = false

				form = new FormData();
				form.append("login", self.login);
				form.append("password", self.pass);

				axios.post('/main_page/login', form)
					.then(function (response) {
						console.log(response , 'response')
						if(response.data.user) {
							location.reload();
						}
						setTimeout(function () {
							$('#loginModal').modal('hide');
						}, 500);
					})
			}
		},
		addReplyComment(reply_id, reply_text) {
			let self = this;
			self.replyId = reply_id
			self.replyText = reply_text
		},
		addComment: function(id) {
			var self = this;
			if(self.commentText) {

				var comment = new FormData();
				comment.append('postId', id);
				comment.append('replyId', self.replyId);
				comment.append('commentText', self.commentText);

				axios.post(
					'/main_page/comment',
					comment
				).then(function (response) {
					self.post.coments.push(response.data.comment)
				});
			}
		},
		refill: function () {
			var self= this;
			if(self.addSum === 0){
				self.invalidSum = true
			}
			else{
				self.invalidSum = false
				sum = new FormData();
				sum.append('sum', self.addSum);
				axios.post('/main_page/add_money', sum)
					.then(function (response) {
						setTimeout(function () {
							$('#addModal').modal('hide');
						}, 500);
					})
			}
		},
		openPost: function (id) {
			var self= this;
			axios
				.get('/main_page/get_post/' + id)
				.then(function (response) {
					self.post = response.data.post;
					if(self.post){
						setTimeout(function () {
							$('#postModal').modal('show');
						}, 500);
					}
				})
		},
		addLike: function (type, id) {
			var self = this;
			const url = '/main_page/like_' + type + '/' + id;
			axios
				.get(url)
				.then(function (response) {
					self.likes = response.data.likes;
				})

		},
		buyPack: function (id) {
			var self= this;
			var pack = new FormData();
			pack.append('id', id);
			axios.post('/main_page/buy_boosterpack', pack)
				.then(function (response) {
					self.amount = response.data.amount
					if(self.amount !== 0){
						setTimeout(function () {
							$('#amountModal').modal('show');
						}, 500);
					}
				})
		}
	}
});

