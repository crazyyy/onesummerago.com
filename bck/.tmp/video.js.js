(function(window,undefined){var document=window.document;(function(){var initializing=false,fnTest=/xyz/.test(function(){xyz;})?/\b_super\b/:/.*/;this.JRClass=function(){};JRClass.extend=function(prop){var _super=this.prototype;initializing=true;var prototype=new this();initializing=false;for(var name in prop){prototype[name]=typeof prop[name]=="function"&&typeof _super[name]=="function"&&fnTest.test(prop[name])?(function(name,fn){return function(){var tmp=this._super;this._super=_super[name];var ret=fn.apply(this,arguments);this._super=tmp;return ret;};})(name,prop[name]):prop[name];}function JRClass(){if(!initializing&&this.init)this.init.apply(this,arguments);}JRClass.prototype=prototype;JRClass.constructor=JRClass;JRClass.extend=arguments.callee;return JRClass;};})();var VideoJS=JRClass.extend({init:function(element,setOptions){if(typeof element=='string'){this.video=document.getElementById(element);}else{this.video=element;}
this.video.player=this;this.options={autoplay:false,preload:true,useBuiltInControls:false,controlsBelow:false,controlsAtStart:false,controlsHiding:true,defaultVolume:0.85,playerFallbackOrder:["html5","flash","links"],flashPlayer:"htmlObject",flashPlayerVersion:false};if(typeof VideoJS.options=="object"){_V_.merge(this.options,VideoJS.options);}
if(typeof setOptions=="object"){_V_.merge(this.options,setOptions);}
if(this.getPreloadAttribute()!==undefined){this.options.preload=this.getPreloadAttribute();}
if(this.getAutoplayAttribute()!==undefined){this.options.autoplay=this.getAutoplayAttribute();}
this.box=this.video.parentNode;this.linksFallback=this.getLinksFallback();this.hideLinksFallback();this.each(this.options.playerFallbackOrder,function(playerType){if(this[playerType+"Supported"]()){this[playerType+"Init"]();return true;}});this.activateElement(this,"player");this.activateElement(this.box,"box");},behaviors:{},elements:{},newBehavior:function(name,activate,functions){this.behaviors[name]=activate;this.extend(functions);},activateElement:function(element,behavior){this.behaviors[behavior].call(this,element);},errors:[],warnings:[],warning:function(warning){this.warnings.push(warning);this.log(warning);},history:[],log:function(event){if(!event){return;}
if(typeof event=="string"){event={type:event};}
if(event.type){this.history.push(event.type);}
if(this.history.length>=50){this.history.shift();}
try{console.log(event.type);}catch(e){try{opera.postError(event.type);}catch(e){}}},setLocalStorage:function(key,value){try{localStorage[key]=value;}
catch(e){if(e.code==22||e.code==1014){this.warning(VideoJS.warnings.localStorageFull);}}},getPreloadAttribute:function(){if(typeof this.video.hasAttribute=="function"&&this.video.hasAttribute("preload")){var preload=this.video.getAttribute("preload");if(preload===""||preload==="true"){return"auto";}
if(preload==="false"){return"none";}
return preload;}},getAutoplayAttribute:function(){if(typeof this.video.hasAttribute=="function"&&this.video.hasAttribute("autoplay")){var autoplay=this.video.getAttribute("autoplay");if(autoplay==="false"){return false;}
return true;}},bufferedPercent:function(){return(this.duration())?this.buffered()[1]/this.duration():0;},each:function(arr,fn){if(!arr||arr.length===0){return;}
for(var i=0,j=arr.length;i<j;i++){if(fn.call(this,arr[i],i)){break;}}},extend:function(obj){for(var attrname in obj){if(obj.hasOwnProperty(attrname)){this[attrname]=obj[attrname];}}}});VideoJS.player=VideoJS.prototype;VideoJS.player.extend({flashSupported:function(){if(!this.flashElement){this.flashElement=this.getFlashElement();}
if(this.flashElement&&this.flashPlayerVersionSupported()){return true;}else{return false;}},flashInit:function(){this.replaceWithFlash();this.element=this.flashElement;this.video.src="";var flashPlayerType=VideoJS.flashPlayers[this.options.flashPlayer];this.extend(VideoJS.flashPlayers[this.options.flashPlayer].api);(flashPlayerType.init.context(this))();},getFlashElement:function(){var children=this.video.children;for(var i=0,j=children.length;i<j;i++){if(children[i].className=="vjs-flash-fallback"){return children[i];}}},replaceWithFlash:function(){if(this.flashElement){this.box.insertBefore(this.flashElement,this.video);this.video.style.display="none";}},flashPlayerVersionSupported:function(){var playerVersion=(this.options.flashPlayerVersion)?this.options.flashPlayerVersion:VideoJS.flashPlayers[this.options.flashPlayer].flashPlayerVersion;return VideoJS.getFlashVersion()>=playerVersion;}});VideoJS.flashPlayers={};VideoJS.flashPlayers.htmlObject={flashPlayerVersion:9,init:function(){return true;},api:{width:function(width){if(width!==undefined){this.element.width=width;this.box.style.width=width+"px";this.triggerResizeListeners();return this;}
return this.element.width;},height:function(height){if(height!==undefined){this.element.height=height;this.box.style.height=height+"px";this.triggerResizeListeners();return this;}
return this.element.height;}}};VideoJS.player.extend({linksSupported:function(){return true;},linksInit:function(){this.showLinksFallback();this.element=this.video;},getLinksFallback:function(){return this.box.getElementsByTagName("P")[0];},hideLinksFallback:function(){if(this.linksFallback){this.linksFallback.style.display="none";}},showLinksFallback:function(){if(this.linksFallback){this.linksFallback.style.display="block";}}});VideoJS.merge=function(obj1,obj2,safe){for(var attrname in obj2){if(obj2.hasOwnProperty(attrname)&&(!safe||!obj1.hasOwnProperty(attrname))){obj1[attrname]=obj2[attrname];}}
return obj1;};VideoJS.extend=function(obj){this.merge(this,obj,true);};VideoJS.extend({setupAllWhenReady:function(options){VideoJS.options=options;VideoJS.DOMReady(VideoJS.setup);},DOMReady:function(fn){VideoJS.addToDOMReady(fn);},setup:function(videos,options){var returnSingular=false,playerList=[],videoElement;if(!videos||videos=="All"){videos=VideoJS.getVideoJSTags();}else if(typeof videos!='object'||videos.nodeType==1){videos=[videos];returnSingular=true;}
for(var i=0;i<videos.length;i++){if(typeof videos[i]=='string'){videoElement=document.getElementById(videos[i]);}else{videoElement=videos[i];}
playerList.push(new VideoJS(videoElement,options));}
return(returnSingular)?playerList[0]:playerList;},getVideoJSTags:function(){var videoTags=document.getElementsByTagName("video"),videoJSTags=[],videoTag;for(var i=0,j=videoTags.length;i<j;i++){videoTag=videoTags[i];if(videoTag.className.indexOf("video-js")!=-1){videoJSTags.push(videoTag);}}
return videoJSTags;},browserSupportsVideo:function(){if(typeof VideoJS.videoSupport!="undefined"){return VideoJS.videoSupport;}
VideoJS.videoSupport=!!document.createElement('video').canPlayType;return VideoJS.videoSupport;},getFlashVersion:function(){if(typeof VideoJS.flashVersion!="undefined"){return VideoJS.flashVersion;}
var version=0,desc;if(typeof navigator.plugins!="undefined"&&typeof navigator.plugins["Shockwave Flash"]=="object"){desc=navigator.plugins["Shockwave Flash"].description;if(desc&&!(typeof navigator.mimeTypes!="undefined"&&navigator.mimeTypes["application/x-shockwave-flash"]&&!navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin)){version=parseInt(desc.match(/^.*\s+([^\s]+)\.[^\s]+\s+[^\s]+$/)[1],10);}}else if(typeof window.ActiveXObject!="undefined"){try{var testObject=new ActiveXObject("ShockwaveFlash.ShockwaveFlash");if(testObject){version=parseInt(testObject.GetVariable("$version").match(/^[^\s]+\s(\d+)/)[1],10);}}
catch(e){}}
VideoJS.flashVersion=version;return VideoJS.flashVersion;},isIE:function(){return!+"\v1";},isIPad:function(){return navigator.userAgent.match(/iPad/i)!==null;},isIPhone:function(){return navigator.userAgent.match(/iPhone/i)!==null;},isIOS:function(){return VideoJS.isIPhone()||VideoJS.isIPad();},iOSVersion:function(){var match=navigator.userAgent.match(/OS (\d+)_/i);if(match&&match[1]){return match[1];}},isAndroid:function(){return navigator.userAgent.match(/Android/i)!==null;},androidVersion:function(){var match=navigator.userAgent.match(/Android (\d+)\./i);if(match&&match[1]){return match[1];}},warnings:{videoNotReady:"Video is not ready yet (try playing the video first).",localStorageFull:"Local Storage is Full"}});if(VideoJS.isIE()){document.createElement("video");}
window.VideoJS=window._V_=VideoJS;VideoJS.player.extend({html5Supported:function(){if(VideoJS.browserSupportsVideo()&&this.canPlaySource()){return true;}else{return false;}},html5Init:function(){this.element=this.video;this.fixPreloading();this.supportProgressEvents();this.volume((localStorage&&localStorage.volume)||this.options.defaultVolume);if(VideoJS.isIOS()){this.options.useBuiltInControls=true;this.iOSInterface();}else if(VideoJS.isAndroid()){this.options.useBuiltInControls=true;this.androidInterface();}
if(!this.options.useBuiltInControls){this.video.controls=false;if(this.options.controlsBelow){_V_.addClass(this.box,"vjs-controls-below");}
this.activateElement(this.video,"playToggle");this.buildStylesCheckDiv();this.buildAndActivatePoster();this.buildBigPlayButton();this.buildAndActivateSpinner();this.buildAndActivateControlBar();this.loadInterface();this.getSubtitles();}},canPlaySource:function(){if(this.canPlaySourceResult){return this.canPlaySourceResult;}
var children=this.video.children;for(var i=0,j=children.length;i<j;i++){if(children[i].tagName.toUpperCase()=="SOURCE"){var canPlay=this.video.canPlayType(children[i].type)||this.canPlayExt(children[i].src);if(canPlay=="probably"||canPlay=="maybe"){this.firstPlayableSource=children[i];this.canPlaySourceResult=true;return true;}}}
this.canPlaySourceResult=false;return false;},canPlayExt:function(src){if(!src){return"";}
var match=src.match(/\.([^\.]+)$/);if(match&&match[1]){var ext=match[1].toLowerCase();if(VideoJS.isAndroid()){if(ext=="mp4"||ext=="m4v"){return"maybe";}}else if(VideoJS.isIOS()){if(ext=="m3u8"){return"maybe";}}}
return"";},forceTheSource:function(){this.video.src=this.firstPlayableSource.src;this.video.load();},fixPreloading:function(){if(typeof this.video.hasAttribute=="function"&&this.video.hasAttribute("preload")&&this.video.preload!="none"){this.video.autobuffer=true;}else{this.video.autobuffer=false;this.video.preload="none";}},supportProgressEvents:function(e){_V_.addListener(this.video,'progress',this.playerOnVideoProgress.context(this));},playerOnVideoProgress:function(event){this.setBufferedFromProgress(event);},setBufferedFromProgress:function(event){if(event.total>0){var newBufferEnd=(event.loaded/event.total)*this.duration();if(newBufferEnd>this.values.bufferEnd){this.values.bufferEnd=newBufferEnd;}}},iOSInterface:function(){if(VideoJS.iOSVersion()<4){this.forceTheSource();}
if(VideoJS.isIPad()){this.buildAndActivateSpinner();}},androidInterface:function(){this.forceTheSource();_V_.addListener(this.video,"click",function(){this.play();});this.buildBigPlayButton();_V_.addListener(this.bigPlayButton,"click",function(){this.play();}.context(this));this.positionBox();this.showBigPlayButtons();},loadInterface:function(){if(!this.stylesHaveLoaded()){if(!this.positionRetries){this.positionRetries=1;}
if(this.positionRetries++<100){setTimeout(this.loadInterface.context(this),10);return;}}
this.hideStylesCheckDiv();this.showPoster();if(this.video.paused!==false){this.showBigPlayButtons();}
if(this.options.controlsAtStart){this.showControlBars();}
this.positionAll();},buildAndActivateControlBar:function(){this.controls=_V_.createElement("div",{className:"vjs-controls"});this.box.appendChild(this.controls);this.activateElement(this.controls,"controlBar");this.activateElement(this.controls,"mouseOverVideoReporter");this.playControl=_V_.createElement("div",{className:"vjs-play-control",innerHTML:"<span></span>"});this.controls.appendChild(this.playControl);this.activateElement(this.playControl,"playToggle");this.progressControl=_V_.createElement("div",{className:"vjs-progress-control"});this.controls.appendChild(this.progressControl);this.progressHolder=_V_.createElement("div",{className:"vjs-progress-holder"});this.progressControl.appendChild(this.progressHolder);this.activateElement(this.progressHolder,"currentTimeScrubber");this.loadProgressBar=_V_.createElement("div",{className:"vjs-load-progress"});this.progressHolder.appendChild(this.loadProgressBar);this.activateElement(this.loadProgressBar,"loadProgressBar");this.playProgressBar=_V_.createElement("div",{className:"vjs-play-progress"});this.progressHolder.appendChild(this.playProgressBar);this.activateElement(this.playProgressBar,"playProgressBar");this.timeControl=_V_.createElement("div",{className:"vjs-time-control"});this.controls.appendChild(this.timeControl);this.currentTimeDisplay=_V_.createElement("span",{className:"vjs-current-time-display",innerHTML:"00:00"});this.timeControl.appendChild(this.currentTimeDisplay);this.activateElement(this.currentTimeDisplay,"currentTimeDisplay");this.timeSeparator=_V_.createElement("span",{innerHTML:" / "});this.timeControl.appendChild(this.timeSeparator);this.durationDisplay=_V_.createElement("span",{className:"vjs-duration-display",innerHTML:"00:00"});this.timeControl.appendChild(this.durationDisplay);this.activateElement(this.durationDisplay,"durationDisplay");this.volumeControl=_V_.createElement("div",{className:"vjs-volume-control",innerHTML:"<div><span></span><span></span><span></span><span></span><span></span><span></span></div>"});this.controls.appendChild(this.volumeControl);this.activateElement(this.volumeControl,"volumeScrubber");this.volumeDisplay=this.volumeControl.children[0];this.activateElement(this.volumeDisplay,"volumeDisplay");this.fullscreenControl=_V_.createElement("div",{className:"vjs-fullscreen-control",innerHTML:"<div><span></span><span></span><span></span><span></span></div>"});this.controls.appendChild(this.fullscreenControl);this.activateElement(this.fullscreenControl,"fullscreenToggle");},buildAndActivatePoster:function(){this.updatePosterSource();if(this.video.poster){this.poster=document.createElement("img");this.box.appendChild(this.poster);this.poster.src=this.video.poster;this.poster.className="vjs-poster";this.activateElement(this.poster,"poster");}else{this.poster=false;}},buildBigPlayButton:function(){this.bigPlayButton=_V_.createElement("div",{className:"vjs-big-play-button",innerHTML:"<span></span>"});this.box.appendChild(this.bigPlayButton);this.activateElement(this.bigPlayButton,"bigPlayButton");},buildAndActivateSpinner:function(){this.spinner=_V_.createElement("div",{className:"vjs-spinner",innerHTML:"<div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>"});this.box.appendChild(this.spinner);this.activateElement(this.spinner,"spinner");},buildStylesCheckDiv:function(){this.stylesCheckDiv=_V_.createElement("div",{className:"vjs-styles-check"});this.stylesCheckDiv.style.position="absolute";this.box.appendChild(this.stylesCheckDiv);},hideStylesCheckDiv:function(){this.stylesCheckDiv.style.display="none";},stylesHaveLoaded:function(){if(this.stylesCheckDiv.offsetHeight!=5){return false;}else{return true;}},positionAll:function(){this.positionBox();this.positionControlBars();this.positionPoster();},positionBox:function(){if(this.videoIsFullScreen){this.box.style.width="";this.element.style.height="";if(this.options.controlsBelow){this.box.style.height="";this.element.style.height=(this.box.offsetHeight-this.controls.offsetHeight)+"px";}}else{this.box.style.width=this.width()+"px";this.element.style.height=this.height()+"px";if(this.options.controlsBelow){this.element.style.height="";}}},getSubtitles:function(){var tracks=this.video.getElementsByTagName("TRACK");for(var i=0,j=tracks.length;i<j;i++){if(tracks[i].getAttribute("kind")=="subtitles"){this.subtitlesSource=tracks[i].getAttribute("src");}}
if(this.subtitlesSource!==undefined){this.loadSubtitles();this.buildSubtitles();}},loadSubtitles:function(){_V_.get(this.subtitlesSource,this.parseSubtitles.context(this));},parseSubtitles:function(subText){var lines=subText.replace("\r",'').split("\n");this.subtitles=[];this.currentSubtitlePosition=0;var i=0;while(i<lines.length){var subtitle={};subtitle.id=lines[i++];if(!subtitle.id){break;}
var time=lines[i++].split(" --> ");subtitle.startTime=this.parseSubtitleTime(time[0]);subtitle.endTime=this.parseSubtitleTime(time[1]);var text=[];while(lines[i].length>0&&lines[i]!="\r"){text.push(lines[i++]);}
subtitle.text=text.join('<br/>');this.subtitles.push(subtitle);i++;}},parseSubtitleTime:function(timeText){var parts=timeText.split(':');var time=0;time+=parseFloat(parts[0])*60*60;time+=parseFloat(parts[1])*60;var seconds=parts[2].split(',');time+=parseFloat(seconds[0]);time=time+parseFloat(seconds[1])/1000;return time;},buildSubtitles:function(){this.subtitlesDisplay=_V_.createElement("div",{className:'vjs-subtitles'});this.box.appendChild(this.subtitlesDisplay);this.activateElement(this.subtitlesDisplay,"subtitlesDisplay");},values:{},addVideoListener:function(type,fn){_V_.addListener(this.video,type,fn.rEvtContext(this));},play:function(){this.video.play();return this;},onPlay:function(fn){this.addVideoListener("play",fn);return this;},pause:function(){this.video.pause();return this;},onPause:function(fn){this.addVideoListener("pause",fn);return this;},paused:function(){return this.video.paused;},currentTime:function(seconds){if(seconds!==undefined){try{this.video.currentTime=seconds;}
catch(e){this.warning(VideoJS.warnings.videoNotReady);}
this.values.currentTime=seconds;return this;}
return this.video.currentTime;},lastSetCurrentTime:function(){return this.values.currentTime;},duration:function(){return this.video.duration;},buffered:function(){if(this.values.bufferStart===undefined){this.values.bufferStart=0;this.values.bufferEnd=0;}
if(this.video.buffered&&this.video.buffered.length>0){var newEnd=this.video.buffered.end(0);if(newEnd>this.values.bufferEnd){this.values.bufferEnd=newEnd;}}
return[this.values.bufferStart,this.values.bufferEnd];},volume:function(percentAsDecimal){if(percentAsDecimal!==undefined){this.values.volume=parseFloat(percentAsDecimal);this.video.volume=this.values.volume;this.setLocalStorage("volume",this.values.volume);return this;}
if(this.values.volume){return this.values.volume;}
return this.video.volume;},onVolumeChange:function(fn){_V_.addListener(this.video,'volumechange',fn.rEvtContext(this));},width:function(width){if(width!==undefined){this.video.width=width;this.box.style.width=width+"px";this.triggerResizeListeners();return this;}
return this.video.offsetWidth;},height:function(height){if(height!==undefined){this.video.height=height;this.box.style.height=height+"px";this.triggerResizeListeners();return this;}
return this.video.offsetHeight;},supportsFullScreen:function(){if(typeof this.video.webkitEnterFullScreen=='function'){if(!navigator.userAgent.match("Chrome")){return true;}}
return false;},enterFullScreen:function(){try{this.video.webkitEnterFullScreen();}catch(e){if(e.code==11){this.warning(VideoJS.warnings.videoNotReady);}}
return this;},onError:function(fn){this.addVideoListener("error",fn);return this;},onEnded:function(fn){this.addVideoListener("ended",fn);return this;}});VideoJS.player.newBehavior("player",function(player){this.onError(this.playerOnVideoError);this.onPlay(this.playerOnVideoPlay);this.onPlay(this.trackCurrentTime);this.onPause(this.playerOnVideoPause);this.onPause(this.stopTrackingCurrentTime);this.onEnded(this.playerOnVideoEnded);this.trackBuffered();this.onBufferedUpdate(this.isBufferFull);},{playerOnVideoError:function(event){this.log(event);this.log(this.video.error);},playerOnVideoPlay:function(event){this.hasPlayed=true;},playerOnVideoPause:function(event){},playerOnVideoEnded:function(event){this.currentTime(0);this.pause();},trackBuffered:function(){this.bufferedInterval=setInterval(this.triggerBufferedListeners.context(this),500);},stopTrackingBuffered:function(){clearInterval(this.bufferedInterval);},bufferedListeners:[],onBufferedUpdate:function(fn){this.bufferedListeners.push(fn);},triggerBufferedListeners:function(){this.each(this.bufferedListeners,function(listener){(listener.context(this))();});},isBufferFull:function(){if(this.bufferedPercent()==1){this.stopTrackingBuffered();}},trackCurrentTime:function(){if(this.currentTimeInterval){clearInterval(this.currentTimeInterval);}
this.currentTimeInterval=setInterval(this.triggerCurrentTimeListeners.context(this),42);this.trackingCurrentTime=true;},stopTrackingCurrentTime:function(){clearInterval(this.currentTimeInterval);this.trackingCurrentTime=false;},currentTimeListeners:[],onCurrentTimeUpdate:function(fn){this.currentTimeListeners.push(fn);},triggerCurrentTimeListeners:function(late,newTime){this.each(this.currentTimeListeners,function(listener){(listener.context(this))(newTime);});},resizeListeners:[],onResize:function(fn){this.resizeListeners.push(fn);},triggerResizeListeners:function(){this.each(this.resizeListeners,function(listener){(listener.context(this))();});}});VideoJS.player.newBehavior("mouseOverVideoReporter",function(element){_V_.addListener(element,"mousemove",this.mouseOverVideoReporterOnMouseMove.context(this));_V_.addListener(element,"mouseout",this.mouseOverVideoReporterOnMouseOut.context(this));},{mouseOverVideoReporterOnMouseMove:function(){this.showControlBars();clearInterval(this.mouseMoveTimeout);this.mouseMoveTimeout=setTimeout(this.hideControlBars.context(this),4000);},mouseOverVideoReporterOnMouseOut:function(event){var parent=event.relatedTarget;while(parent&&parent!==this.box){parent=parent.parentNode;}
if(parent!==this.box){this.hideControlBars();}}});VideoJS.player.newBehavior("box",function(element){this.positionBox();_V_.addClass(element,"vjs-paused");this.activateElement(element,"mouseOverVideoReporter");this.onPlay(this.boxOnVideoPlay);this.onPause(this.boxOnVideoPause);},{boxOnVideoPlay:function(){_V_.removeClass(this.box,"vjs-paused");_V_.addClass(this.box,"vjs-playing");},boxOnVideoPause:function(){_V_.removeClass(this.box,"vjs-playing");_V_.addClass(this.box,"vjs-paused");}});VideoJS.player.newBehavior("poster",function(element){this.activateElement(element,"mouseOverVideoReporter");this.activateElement(element,"playButton");this.onPlay(this.hidePoster);this.onEnded(this.showPoster);this.onResize(this.positionPoster);},{showPoster:function(){if(!this.poster){return;}
this.poster.style.display="block";this.positionPoster();},positionPoster:function(){if(!this.poster||this.poster.style.display=='none'){return;}
this.poster.style.height=this.height()+"px";this.poster.style.width=this.width()+"px";},hidePoster:function(){if(!this.poster){return;}
this.poster.style.display="none";},updatePosterSource:function(){if(!this.video.poster){var images=this.video.getElementsByTagName("img");if(images.length>0){this.video.poster=images[0].src;}}}});VideoJS.player.newBehavior("controlBar",function(element){if(!this.controlBars){this.controlBars=[];this.onResize(this.positionControlBars);}
this.controlBars.push(element);_V_.addListener(element,"mousemove",this.onControlBarsMouseMove.context(this));_V_.addListener(element,"mouseout",this.onControlBarsMouseOut.context(this));},{showControlBars:function(){if(!this.options.controlsAtStart&&!this.hasPlayed){return;}
this.each(this.controlBars,function(bar){bar.style.display="block";});},positionControlBars:function(){this.updatePlayProgressBars();this.updateLoadProgressBars();},hideControlBars:function(){if(this.options.controlsHiding&&!this.mouseIsOverControls){this.each(this.controlBars,function(bar){bar.style.display="none";});}},onControlBarsMouseMove:function(){this.mouseIsOverControls=true;},onControlBarsMouseOut:function(event){this.mouseIsOverControls=false;}});VideoJS.player.newBehavior("playToggle",function(element){_V_.addListener(element,"click",this.onPlayToggleClick.context(this));},{onPlayToggleClick:function(event){if(this.paused()){this.play();}else{this.pause();}}});VideoJS.player.newBehavior("playButton",function(element){_V_.addListener(element,"click",this.onPlayButtonClick.context(this));},{onPlayButtonClick:function(event){this.play();}});VideoJS.player.newBehavior("pauseButton",function(element){_V_.addListener(element,"click",this.onPauseButtonClick.context(this));},{onPauseButtonClick:function(event){this.pause();}});VideoJS.player.newBehavior("playProgressBar",function(element){if(!this.playProgressBars){this.playProgressBars=[];this.onCurrentTimeUpdate(this.updatePlayProgressBars);}
this.playProgressBars.push(element);},{updatePlayProgressBars:function(newTime){var progress=(newTime!==undefined)?newTime/this.duration():this.currentTime()/this.duration();if(isNaN(progress)){progress=0;}
this.each(this.playProgressBars,function(bar){if(bar.style){bar.style.width=_V_.round(progress*100,2)+"%";}});}});VideoJS.player.newBehavior("loadProgressBar",function(element){if(!this.loadProgressBars){this.loadProgressBars=[];}
this.loadProgressBars.push(element);this.onBufferedUpdate(this.updateLoadProgressBars);},{updateLoadProgressBars:function(){this.each(this.loadProgressBars,function(bar){if(bar.style){bar.style.width=_V_.round(this.bufferedPercent()*100,2)+"%";}});}});VideoJS.player.newBehavior("currentTimeDisplay",function(element){if(!this.currentTimeDisplays){this.currentTimeDisplays=[];this.onCurrentTimeUpdate(this.updateCurrentTimeDisplays);}
this.currentTimeDisplays.push(element);},{updateCurrentTimeDisplays:function(newTime){if(!this.currentTimeDisplays){return;}
var time=(newTime)?newTime:this.currentTime();this.each(this.currentTimeDisplays,function(dis){dis.innerHTML=_V_.formatTime(time);});}});VideoJS.player.newBehavior("durationDisplay",function(element){if(!this.durationDisplays){this.durationDisplays=[];this.onCurrentTimeUpdate(this.updateDurationDisplays);}
this.durationDisplays.push(element);},{updateDurationDisplays:function(){if(!this.durationDisplays){return;}
this.each(this.durationDisplays,function(dis){if(this.duration()){dis.innerHTML=_V_.formatTime(this.duration());}});}});VideoJS.player.newBehavior("currentTimeScrubber",function(element){_V_.addListener(element,"mousedown",this.onCurrentTimeScrubberMouseDown.rEvtContext(this));},{onCurrentTimeScrubberMouseDown:function(event,scrubber){event.preventDefault();this.currentScrubber=scrubber;this.stopTrackingCurrentTime();this.videoWasPlaying=!this.paused();this.pause();_V_.blockTextSelection();this.setCurrentTimeWithScrubber(event);_V_.addListener(document,"mousemove",this.onCurrentTimeScrubberMouseMove.rEvtContext(this));_V_.addListener(document,"mouseup",this.onCurrentTimeScrubberMouseUp.rEvtContext(this));},onCurrentTimeScrubberMouseMove:function(event){this.setCurrentTimeWithScrubber(event);},onCurrentTimeScrubberMouseUp:function(event){_V_.unblockTextSelection();document.removeEventListener("mousemove",this.onCurrentTimeScrubberMouseMove,false);document.removeEventListener("mouseup",this.onCurrentTimeScrubberMouseUp,false);this.trackCurrentTime();if(this.videoWasPlaying){this.play();}},setCurrentTimeWithScrubber:function(event){var newProgress=_V_.getRelativePosition(event.pageX,this.currentScrubber);var newTime=newProgress*this.duration();this.triggerCurrentTimeListeners(0,newTime);if(newTime==this.duration()){newTime=newTime-0.1;}
this.currentTime(newTime);}});VideoJS.player.newBehavior("volumeDisplay",function(element){if(!this.volumeDisplays){this.volumeDisplays=[];this.onVolumeChange(this.updateVolumeDisplays);}
this.volumeDisplays.push(element);this.updateVolumeDisplay(element);},{updateVolumeDisplays:function(){if(!this.volumeDisplays){return;}
this.each(this.volumeDisplays,function(dis){this.updateVolumeDisplay(dis);});},updateVolumeDisplay:function(display){var volNum=Math.ceil(this.volume()*6);this.each(display.children,function(child,num){if(num<volNum){_V_.addClass(child,"vjs-volume-level-on");}else{_V_.removeClass(child,"vjs-volume-level-on");}});}});VideoJS.player.newBehavior("volumeScrubber",function(element){_V_.addListener(element,"mousedown",this.onVolumeScrubberMouseDown.rEvtContext(this));},{onVolumeScrubberMouseDown:function(event,scrubber){_V_.blockTextSelection();this.currentScrubber=scrubber;this.setVolumeWithScrubber(event);_V_.addListener(document,"mousemove",this.onVolumeScrubberMouseMove.rEvtContext(this));_V_.addListener(document,"mouseup",this.onVolumeScrubberMouseUp.rEvtContext(this));},onVolumeScrubberMouseMove:function(event){this.setVolumeWithScrubber(event);},onVolumeScrubberMouseUp:function(event){this.setVolumeWithScrubber(event);_V_.unblockTextSelection();document.removeEventListener("mousemove",this.onVolumeScrubberMouseMove,false);document.removeEventListener("mouseup",this.onVolumeScrubberMouseUp,false);},setVolumeWithScrubber:function(event){var newVol=_V_.getRelativePosition(event.pageX,this.currentScrubber);this.volume(newVol);}});VideoJS.player.newBehavior("fullscreenToggle",function(element){_V_.addListener(element,"click",this.onFullscreenToggleClick.context(this));},{onFullscreenToggleClick:function(event){if(!this.videoIsFullScreen){this.fullscreenOn();}else{this.fullscreenOff();}},fullscreenOn:function(){if(!this.nativeFullscreenOn()){this.videoIsFullScreen=true;this.docOrigOverflow=document.documentElement.style.overflow;_V_.addListener(document,"keydown",this.fullscreenOnEscKey.rEvtContext(this));_V_.addListener(window,"resize",this.fullscreenOnWindowResize.rEvtContext(this));document.documentElement.style.overflow='hidden';_V_.addClass(this.box,"vjs-fullscreen");this.positionAll();}},nativeFullscreenOn:function(){if(this.supportsFullScreen()){return this.enterFullScreen();}else{return false;}},fullscreenOff:function(){this.videoIsFullScreen=false;document.removeEventListener("keydown",this.fullscreenOnEscKey,false);window.removeEventListener("resize",this.fullscreenOnWindowResize,false);document.documentElement.style.overflow=this.docOrigOverflow;_V_.removeClass(this.box,"vjs-fullscreen");this.positionAll();},fullscreenOnWindowResize:function(event){this.positionControlBars();},fullscreenOnEscKey:function(event){if(event.keyCode==27){this.fullscreenOff();}}});VideoJS.player.newBehavior("bigPlayButton",function(element){if(!this.bigPlayButtons){this.bigPlayButtons=[];this.onPlay(this.bigPlayButtonsOnPlay);this.onEnded(this.bigPlayButtonsOnEnded);}
this.bigPlayButtons.push(element);this.activateElement(element,"playButton");},{bigPlayButtonsOnPlay:function(event){this.hideBigPlayButtons();},bigPlayButtonsOnEnded:function(event){this.showBigPlayButtons();},showBigPlayButtons:function(){this.each(this.bigPlayButtons,function(element){element.style.display="block";});},hideBigPlayButtons:function(){this.each(this.bigPlayButtons,function(element){element.style.display="none";});}});VideoJS.player.newBehavior("spinner",function(element){if(!this.spinners){this.spinners=[];_V_.addListener(this.video,"loadeddata",this.spinnersOnVideoLoadedData.context(this));_V_.addListener(this.video,"loadstart",this.spinnersOnVideoLoadStart.context(this));_V_.addListener(this.video,"seeking",this.spinnersOnVideoSeeking.context(this));_V_.addListener(this.video,"seeked",this.spinnersOnVideoSeeked.context(this));_V_.addListener(this.video,"canplay",this.spinnersOnVideoCanPlay.context(this));_V_.addListener(this.video,"canplaythrough",this.spinnersOnVideoCanPlayThrough.context(this));_V_.addListener(this.video,"waiting",this.spinnersOnVideoWaiting.context(this));_V_.addListener(this.video,"stalled",this.spinnersOnVideoStalled.context(this));_V_.addListener(this.video,"suspend",this.spinnersOnVideoSuspend.context(this));_V_.addListener(this.video,"playing",this.spinnersOnVideoPlaying.context(this));_V_.addListener(this.video,"timeupdate",this.spinnersOnVideoTimeUpdate.context(this));}
this.spinners.push(element);},{showSpinners:function(){this.each(this.spinners,function(spinner){spinner.style.display="block";});clearInterval(this.spinnerInterval);this.spinnerInterval=setInterval(this.rotateSpinners.context(this),100);},hideSpinners:function(){this.each(this.spinners,function(spinner){spinner.style.display="none";});clearInterval(this.spinnerInterval);},spinnersRotated:0,rotateSpinners:function(){this.each(this.spinners,function(spinner){spinner.style.WebkitTransform='scale(0.5) rotate('+this.spinnersRotated+'deg)';spinner.style.MozTransform='scale(0.5) rotate('+this.spinnersRotated+'deg)';});if(this.spinnersRotated==360){this.spinnersRotated=0;}
this.spinnersRotated+=45;},spinnersOnVideoLoadedData:function(event){this.hideSpinners();},spinnersOnVideoLoadStart:function(event){this.showSpinners();},spinnersOnVideoSeeking:function(event){},spinnersOnVideoSeeked:function(event){},spinnersOnVideoCanPlay:function(event){},spinnersOnVideoCanPlayThrough:function(event){this.hideSpinners();},spinnersOnVideoWaiting:function(event){this.showSpinners();},spinnersOnVideoStalled:function(event){},spinnersOnVideoSuspend:function(event){},spinnersOnVideoPlaying:function(event){this.hideSpinners();},spinnersOnVideoTimeUpdate:function(event){if(this.spinner.style.display=="block"){this.hideSpinners();}}});VideoJS.player.newBehavior("subtitlesDisplay",function(element){if(!this.subtitlesDisplays){this.subtitlesDisplays=[];_V_.addListener(this.video,"timeupdate",this.subtitlesDisplaysOnVideoTimeUpdate.context(this));}
this.subtitlesDisplays.push(element);},{subtitlesDisplaysOnVideoTimeUpdate:function(){if(this.subtitles){var x=0;while(x<this.subtitles.length&&this.video.currentTime>this.subtitles[x].endTime){if(this.subtitles[x].showing){this.subtitles[x].showing=false;this.updateSubtitlesDisplays("");}
this.currentSubtitlePosition++;x=this.currentSubtitlePosition;}
if(this.currentSubtitlePosition>=this.subtitles.length){return;}
if(this.video.currentTime>=this.subtitles[x].startTime&&this.video.currentTime<=this.subtitles[x].endTime){this.updateSubtitlesDisplays(this.subtitles[x].text);this.subtitles[x].showing=true;}}},updateSubtitlesDisplays:function(val){this.each(this.subtitlesDisplays,function(disp){disp.innerHTML=val;});}});VideoJS.extend({addClass:function(element,classToAdd){if((" "+element.className+" ").indexOf(" "+classToAdd+" ")==-1){element.className=element.className===""?classToAdd:element.className+" "+classToAdd;}},removeClass:function(element,classToRemove){if(element.className.indexOf(classToRemove)==-1){return;}
var classNames=element.className.split(/\s+/);classNames.splice(classNames.lastIndexOf(classToRemove),1);element.className=classNames.join(" ");},createElement:function(tagName,attributes){return this.merge(document.createElement(tagName),attributes);},blockTextSelection:function(){document.body.focus();document.onselectstart=function(){return false;};},unblockTextSelection:function(){document.onselectstart=function(){return true;};},formatTime:function(secs){var seconds=Math.round(secs);var minutes=Math.floor(seconds/60);minutes=(minutes>=10)?minutes:"0"+minutes;seconds=Math.floor(seconds%60);seconds=(seconds>=10)?seconds:"0"+seconds;return minutes+":"+seconds;},getRelativePosition:function(x,relativeElement){return Math.max(0,Math.min(1,(x-this.findPosX(relativeElement))/relativeElement.offsetWidth));},findPosX:function(obj){var curleft=obj.offsetLeft;while(obj=obj.offsetParent){curleft+=obj.offsetLeft;}
return curleft;},getComputedStyleValue:function(element,style){return window.getComputedStyle(element,null).getPropertyValue(style);},round:function(num,dec){if(!dec){dec=0;}
return Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);},addListener:function(element,type,handler){if(element.addEventListener){element.addEventListener(type,handler,false);}else if(element.attachEvent){element.attachEvent("on"+type,handler);}},removeListener:function(element,type,handler){if(element.removeEventListener){element.removeEventListener(type,handler,false);}else if(element.attachEvent){element.detachEvent("on"+type,handler);}},get:function(url,onSuccess){if(typeof XMLHttpRequest=="undefined"){XMLHttpRequest=function(){try{return new ActiveXObject("Msxml2.XMLHTTP.6.0");}catch(e){}
try{return new ActiveXObject("Msxml2.XMLHTTP.3.0");}catch(f){}
try{return new ActiveXObject("Msxml2.XMLHTTP");}catch(g){}
throw new Error("This browser does not support XMLHttpRequest.");};}
var request=new XMLHttpRequest();request.open("GET",url);request.onreadystatechange=function(){if(request.readyState==4&&request.status==200){onSuccess(request.responseText);}}.context(this);request.send();},bindDOMReady:function(){if(document.readyState==="complete"){return VideoJS.onDOMReady();}
if(document.addEventListener){document.addEventListener("DOMContentLoaded",VideoJS.DOMContentLoaded,false);window.addEventListener("load",VideoJS.onDOMReady,false);}else if(document.attachEvent){document.attachEvent("onreadystatechange",VideoJS.DOMContentLoaded);window.attachEvent("onload",VideoJS.onDOMReady);}},DOMContentLoaded:function(){if(document.addEventListener){document.removeEventListener("DOMContentLoaded",VideoJS.DOMContentLoaded,false);VideoJS.onDOMReady();}else if(document.attachEvent){if(document.readyState==="complete"){document.detachEvent("onreadystatechange",VideoJS.DOMContentLoaded);VideoJS.onDOMReady();}}},DOMReadyList:[],addToDOMReady:function(fn){if(VideoJS.DOMIsReady){fn.call(document);}else{VideoJS.DOMReadyList.push(fn);}},DOMIsReady:false,onDOMReady:function(){if(VideoJS.DOMIsReady){return;}
if(!document.body){return setTimeout(VideoJS.onDOMReady,13);}
VideoJS.DOMIsReady=true;if(VideoJS.DOMReadyList){for(var i=0;i<VideoJS.DOMReadyList.length;i++){VideoJS.DOMReadyList[i].call(document);}
VideoJS.DOMReadyList=null;}}});VideoJS.bindDOMReady();Function.prototype.context=function(obj){var method=this,temp=function(){return method.apply(obj,arguments);};return temp;};Function.prototype.evtContext=function(obj){var method=this,temp=function(){var origContext=this;return method.call(obj,arguments[0],origContext);};return temp;};Function.prototype.rEvtContext=function(obj,funcParent){if(this.hasContext===true){return this;}
if(!funcParent){funcParent=obj;}
for(var attrname in funcParent){if(funcParent[attrname]==this){funcParent[attrname]=this.evtContext(obj);funcParent[attrname].hasContext=true;return funcParent[attrname];}}
return this.evtContext(obj);};if(window.jQuery){(function($){$.fn.VideoJS=function(options){this.each(function(){VideoJS.setup(this,options);});return this;};$.fn.player=function(){return this[0].player;};})(jQuery);}
window.VideoJS=window._V_=VideoJS;})(window);