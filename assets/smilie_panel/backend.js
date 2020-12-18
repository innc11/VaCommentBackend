
// 自动添加额外表情
// function auto_fill_into_customset() {
// 	let ab = []

// 	$('.scan .td img, #more .fix img').each(function(){
// 		let src = $(this).attr('src')
// 		let start = src.lastIndexOf('/')
// 		let end = src.lastIndexOf('.')
// 		ab.push([src.substring(start+1, end), src])
// 	})

// 	let content = ''

// 	for(let each of ab.sort(sortfun)) {
// 		content += ':' + each[0] + ':|' + each[1] + '\n'
// 	}

// 	$('#customset-0-4').val('')
// 	$('#customset-0-4').val(content.trim())
	
// 	alert('已添加 '+ab.length + '条表情')
// 	console.log('已添加 '+ab.length + '条表情')
// }

// function sortfun(obj1, obj2) {
// 	return obj1[0].localeCompare(obj2[0], "zh");
// }


let id_smsetsort = '#smilies-sorted'
let id_smdisable = '#smilies-disabled'

function setEnable(smilieSetName, enable) {
	var enableList = []
	$('.smilie-set').each(function() {
		if ($(this).attr('disabled'))
			enableList.push($(this).attr('smilie-set-name'))
	})
	$(id_smdisable).val(JSON.stringify(enableList))
}

function sortfun(obj1, obj2) {
	obj1 = obj1[0]
	obj2 = obj2[0]
	
	let sorted = JSON.parse($(id_smsetsort).val())

	function getIndex(el) {
		for (let i = 0; i < sorted.length; i++) {
			if (el == sorted[i]) return i
		}
	}

	if(getIndex(obj1) < getIndex(obj2))
		return -1
	if(getIndex(obj1) > getIndex(obj2))
		return 1
	if(getIndex(obj1) == getIndex(obj2))
		return obj1.localeCompare(obj2, "zh")
}

function setEnabledState() {
	$('.smilie-set').each(function() {
		let _this = $(this)
		let smilieSetName = _this.attr('smilie-set-name')
		let disbleds = JSON.parse($(id_smdisable).val())
		if ($.inArray(smilieSetName, disbleds)!=-1) {
			_this.attr('disabled', 'true')
			_this.css('opacity', 0.2)
		}
	})
}

function sortEffect() {
	var reordered = function(elements) {
		var sorted = []
		elements.each(function() {
			sorted.push($(this).attr('smilie-set-name'))
		})
		$(id_smsetsort).val(JSON.stringify(sorted))
	}
	
	$('.gridly').gridly({
		base: 134,
		gutter: 5,
		columns: 4,
		callbacks: {reordered: reordered}
	})
}


// 点击监听器，设置启用或者禁止
function addClickListenersForSmilies() {
	$('.smilie-set-preview').click(function() {
		let _this = $(this).parent()

		if (!_this.attr('disabled')) {
			_this.css('opacity', 0.2)
			_this.attr('disabled', 'true')
			setEnable(_this.attr('smilie-set-name'), false)
		} else {
			_this.css('opacity', 1)
			_this.attr('disabled', null)
			setEnable(_this.attr('smilie-set-name'), true)
		}
	})
}

// 扫描新增的表情包
function scanNewSmilieSet(currentSmilieSetsWithPreview) {
	let sorted = JSON.parse($(id_smsetsort).val())
	let foundNew = false

	for (let smset of currentSmilieSetsWithPreview) {
		if ($.inArray(smset[0], sorted)==-1) {
			sorted.push(smset[0])
			foundNew = true
		}
	}

	if (foundNew)
		$(id_smsetsort).val(JSON.stringify(sorted))
}

/*
function addAutoHide() {
	//弹窗选项显隐
	var al = $("#allowpop-1"),
		an = $("#allowpop-0"),
		op = $("#typecho-option-item-width-9, #typecho-option-item-radius-10, #typecho-option-item-bcolor-11, #typecho-option-item-shadow-12");
	if (!al.is(":checked")) op.hide();
	al.click(function(){
		op.show();
	});
	an.click(function(){
		op.hide();
	});
}
*/

$(function(){
	$.get('/smilie_api',
		function(data, textStatus, jqXHR) {
			let urlHeader = data[0]
			let smilieSetsWithPreview = data[1]

			let html = ''
			for (let smilieSet of smilieSetsWithPreview.sort(sortfun)) {
				let name = smilieSet[0]
				let preview = smilieSet[1]

				let imgTag = '<div class="smilie-set-preview" style="background-image: url('+urlHeader + name + '/' + preview+')"></div>'
				let smilieSetTag = '<div class="smilie-set-label">' + name + '</div>'
				html += '<div smilie-set-name="' + name + '" class="smilie-set">' + imgTag + smilieSetTag + '</div>'
			}
			$('.all-smilie-set').html(html)

			sortEffect() // 对"表情包"进行排序
			setEnabledState() // 对"表情包"设置启禁状态
			addClickListenersForSmilies() //  对"表情包"设置设置启禁切换监听器
			scanNewSmilieSet(smilieSetsWithPreview) //  扫描新增的表情包
		}
	)
})