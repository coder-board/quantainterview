(function() {
	var vm = new Vue({
		el: 'body',
		data: {
			status: {
				isLoading: true
			},
			count: 0,
			data: []
		},
		ready: function() {
			this.getData();
		},
		methods: {
			getData: function() {
				this.$http.get('../phpCtrl/getCount.php').then(function(res) {
					if (res.json().result == 1) {
						var data = res.json().data;
						this.$set('data', data);
						this.status.isLoading = false;
						for (key in data) {
							this.count += ~~data[key]['value'];
						}
					} else {
						location.href = './login.html';
					}
				});
			}
		}
	});
})();