(function () {
	var dropArea = document.getElementById("drop-area"),
		fileList = document.getElementById("file-list");
	try{
		function uploadFile (file) {
			var li = document.createElement("li"),
				div = document.createElement("div"),
				img,
				progressBarContainer = document.createElement("div"),
				progressBar = document.createElement("div"),
				reader,
				xhr,
				fileInfo;
	
			li.appendChild(div);
	
			progressBarContainer.className = "progress-bar-container";
			progressBar.className = "progress-bar";
			progressBarContainer.appendChild(progressBar);
			li.appendChild(progressBarContainer);
	
			/*
				If the file is an image and the web browser supports FileReader,
				present a preview in the file list
			*/
			if (typeof FileReader !== "undefined" && (/image/i).test(file.type)) {
				img = document.createElement("img");
				a = document.createElement("a");
				li.appendChild(a).appendChild(img);
				reader = new FileReader();
				reader.onload = (function (theImg) {
					return function (evt) {
						theImg.src = evt.target.result;
					};
				}(img));
				reader.readAsDataURL(file);
			}
	
			// Uploading - for Firefox, Google Chrome and Safari
			xhr = new XMLHttpRequest();
	
			// Update progress bar
			xhr.upload.addEventListener("progress", function (evt) {
				if (evt.lengthComputable) {
					progressBar.style.width = (evt.loaded / evt.total) * 100 + "%";
				}
				else {
					// No data to calculate on
				}
			}, false);
	
			// File uploaded
			xhr.addEventListener("load", function () {
				progressBarContainer.className += " uploaded";
				//progressBar.innerHTML = "Uploaded!";
			}, false);
	
			xhr.open("post", "upload/upload.php", true);
	
			// Set appropriate headers
			xhr.setRequestHeader("Content-Type", "multipart/form-data");
			xhr.setRequestHeader("X-File-Name", file.fileName);
			xhr.setRequestHeader("X-File-Size", file.fileSize);
			xhr.setRequestHeader("X-File-Type", file.type);
	
			// Send the file (doh)
			xhr.send(file);
	
			// Present file info and append it to the list of files
			/*
			fileInfo = "<div><strong>Name:</strong> " + file.name + "</div>";
			fileInfo += "<div><strong>Size:</strong> " + parseInt(file.size / 1024, 10) + " kb</div>";
			fileInfo += "<div><strong>Type:</strong> " + file.type + "</div>";
			div.innerHTML = fileInfo;
			*/
			fileList.appendChild(li);
		}
	
		function traverseFiles (files) {
			if (typeof files !== "undefined") {
				for (var i=0, l=files.length; i<l; i++) {
					uploadFile(files[i]);
				}
			}
			else {
				fileList.innerHTML = "No support for the File API in this web browser";
			}
		}
	
		dropArea.addEventListener("dragleave", function (evt) {
			var target = evt.target;
	
			evt.preventDefault();
			evt.stopPropagation();
		}, false);
	
		dropArea.addEventListener("dragenter", function (evt) {
			//this.className = "over";
			document.getElementById("droplabel").innerHTML = "Drop Here";
			evt.preventDefault();
			evt.stopPropagation();
		}, false);
	
		dropArea.addEventListener("dragover", function (evt) {
			evt.preventDefault();
			evt.stopPropagation();
		}, false);
	
		dropArea.addEventListener("drop", function (evt) {
			traverseFiles(evt.dataTransfer.files);
			document.getElementById("droplabel").innerHTML = "Add New";
			evt.preventDefault();
			evt.stopPropagation();
		}, false);
	} catch (e) {}
})();