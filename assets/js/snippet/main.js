$("input#main-search").focus(function(){
	$(this).parent().addClass("focused");
}).blur(function(){
	$(this).parent().removeClass("focused");
});

function copyToClipboard (element) {
	var $temp = $("<textarea>");
	var brRegex = /<br\s*[\/]?>/gi;
	$("body").append($temp);
	$temp.val($(element).parent().next().last().find( "code" ).html().replace(brRegex, "\r\n")).select();
	document.execCommand("copy");
	$temp.remove();

	alertify.set('notifier','position', 'top-center');
	alertify.success('🍩 Done 🎈');
}

function showCode (element) {

	$(element).parent().prev().find( "p" ).toggle();
	$(element).parent().next().last().find( "pre" ).toggle();
	$(element).text(function(i, text){
		return text === "Show" ? "Hide" : "Show";
	})
}

var selectedFilter = 'All';
function setFilter(element) {
	$(element).parent().find("button").removeClass();
	$(element).parent().find("button").addClass("btn btn-light");

	$(element).removeClass();
	$(element).addClass("btn btn-success");

	selectedFilter = $(element).html();
	sendAjax()
}

function sendAjax() {
	$.ajax({
		type: "get",
		url: "/api/getSnippetData",
		data: {
			"searchFor": $('input#main-search').val(),
			"selectedFilter": selectedFilter
		},
		success: function (response) {
			$('div.results-div').empty();
			if(response != 'NULL')
				render(response);
		}
	});
}

$('input#main-search').keyup(function() {
	sendAjax();
});
sendAjax();

function render(data) {
	var q = '';
	$.each(data, function( index, value ) {
		var tagsAll = '';
		$.each(value.tags, function( index, tag ) {
			tagsAll += `<span class="badge badge-primary">${tag}</span>`;
		});
		var paragraph = '';
		if (value.description != '')
			paragraph = '<br><p>' + value.description + "</p>";
		q = `
	<div class="card results">
		<div class="card-body row">
			<div class="col-6">
				<h1 class="card-title mb-0">${value.title}</h1>
				${tagsAll}
				${paragraph}
			</div>
			<div class="col-6 ta-r">
				<button class="btn btn-dark btn-sm" onclick="showCode(this)">Show</button>
				<button class="btn btn-danger btn-sm" onclick="copyToClipboard(this)">Copy</button>
			</div>
			<div class="col-12">
				<code hidden>${value.data}</code>
				<pre><code>${value.data}</code></pre>
			</div>
		</div>
	</div>
		`;
		$('div.results-div').append(q);
	});
	$('pre code').each(function(i, block) {
		hljs.highlightBlock(block);
	});
}

