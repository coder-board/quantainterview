(function() {
	
	Vue.config.devtools = false;
	Vue.use(Vue.resource);
	var vm = new Vue({
		el: 'body',
		data: {
			timer: null,
			output_url: '../phpCtrl/output.php',
			status: {
				isLoading: true,
				isSelecting: true
			},
			option: {		// 筛选的条件
				year: (new Date()).getFullYear(),
				department: 0,
				turn: 0,
				turn_result: 0,
				keyword: ''
			},
			formGroup: [{
				key: 'year',
				name: '年份',
				value: []
			}, {
				key: 'department',
				name: '部门',
				value: [
					{"depart_name": '所有', "depart_id": '0'}
				]
			}, {
				key: 'turn',
				name: '面试轮次',
				value: [
					{"tag": "所有", "id": 0}, 
					{"tag": "一面", "id": 1},
					{"tag": "二面", "id": 2}
				]
			},{
				key: 'sex',
				name: '性别',
				value: [
					{"tag": "不限", "id": 3}, 
					{"tag": "男", "id": 2}, 
					{"tag": "女", "id": 1},
				] 
			},{
				key: 'turn_result',
				name: '面试结果筛选',
				value: [
					{"tag": "所有", "id": 0}, 
					{"tag": "不通过", "id": 1},
					{"tag": "待定", "id": 2},
					{"tag": "通过", "id": 3}
				] 
			}],
			modal: {
				type: 'notice',
				status: false,
				text: ''
			},
			mainData: []
		},
		ready: function() {
			this.getFormGroup();
			location.search.indexOf("?") != -1 && this.setOption();
			this.renderData();
		},
		methods: {
			parseParam: function(json) {
				var str = '?';
				for (key in json) {
					str += key + '=' + json[key] + '&';
				}
				str = str.substring(0, str.length-1);
				return str;
			},
			getFormGroup: function() {
				this.$http.get('../phpCtrl/getFormGroup.php').then(function(res) {
					var json = this.formGroup[1].value.concat(res.json().data.depart);
					this.$set('formGroup[0].value', res.json().data.year);
					this.$set('formGroup[1].value', json);
					this.option.year = res.json().data.year[0]['id'];
				});
			},
			setOption: function() {
				var search = location.search, json = {
					year: (new Date()).getFullYear(),
					department: 0,
					turn: 0,
					turn_result: 0,
					keyword: ''
				};
				if (search.indexOf("?") != -1) {
					search = search.substr(1, search.length).split("&");
					for (var i = 0, cell, length = search.length; i < length; i++) {
						cell = search[i].split('=');
						json[cell[0]] = cell[1];
					}
					this.$set('option', json);
				}
			},
			renderData: function() {
				this.output_url = '../phpCtrl/output.php' + this.parseParam(this.option);
				this.status.isSelecting = true;
				this.$http.get('../phpCtrl/overview.php', {
					emulateJSON: true,
					params: this.option
				}).then(function(res) {
					console.log(res);
					if (res.json().result == 1) {
						this.$set('mainData', res.json().data);
						this.status.isLoading = false;
						this.status.isSelecting = false;

					} else if (res.json().result = -1) {
						location.href = './login.html';

					} else {
						alert('请求出现异常，请重新登录');
						location.href = './login.html';
					}
				});
			},
			onFocus: function(obj) {
				console.log(obj);
				// document.getElementById(obj).setAttribute('size', 20);
			},
			delay: function() {
				this.timer && clearTimeout(this.timer);
				this.timer = setTimeout(this.renderData, 300);
			},
			deleteItem: function(id) {
				this.$http.get('../phpCtrl/delete.php', {
					emulateJSON: true,
					params: {
						'itv_code': id
					}
				}).then(function(res) {
					if (res.json().result == 1) {
						this.status.isLoading = true;
						this.renderData();
					}
					this.onModal('notice', res.json().msg, res.json().data);
				});
			},
			onModal: function(type, text, data) {
				this.modal.type = type;
				this.modal.text = text;
				this.modal.data = data;
				this.modal.status = true;
			},
			offModal: function() {
				this.modal.status = false;
			}
		}
	});

	/* 过滤器toDepartment： 数字 转 部门*/
	Vue.filter('toDepartment', function(value) {
		var json = vm.formGroup[1].value
		for (var key in json) {
			if (json[key]['depart_id'] == value) {
				return json[key]['depart_name'];
			}
		}
		return '无';
	});

	/* 过滤器toSex： 数字 转 sex*/
	Vue.filter('toSex', function(value) {
		switch(value) {
			case '2': return '男';
			case '1': return '女';
			// default: return '女';
		}
	});

	/* 过滤器toResult： 数字 转 面试结果*/
	Vue.filter('toResult', function(value) {
		switch(value) {
			case '3': return '通过';
			case '2': return '待定';
			case '1': return '不通过';
			default: return '无记录';
		}
	});

	Vue.filter('each', function(json) {
		return json.itv_code + '--' + json.name + '--' + json.phone;
	});
})();