
// usage: log('inside coolFunc', this, arguments);
// paulirish.com/2009/log-a-lightweight-wrapper-for-consolelog/
window.log = function(){
  log.history = log.history || [];   // store logs to an array for reference
  log.history.push(arguments);
  if(this.console) {
    arguments.callee = arguments.callee.caller;
    var newarr = [].slice.call(arguments);
    (typeof console.log === 'object' ? log.apply.call(console.log, console, newarr) : console.log.apply(console, newarr));
  }
};

// make it safe to use console.log always
(function(b){function c(){}for(var d="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,timeStamp,profile,profileEnd,time,timeEnd,trace,warn".split(","),a;a=d.pop();){b[a]=b[a]||c}})((function(){try
{console.log();return window.console;}catch(err){return window.console={};}})());


// place any jQuery/helper plugins in here, instead of separate, slower script files.

// DATEPICKER
Date.dayNames=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];Date.abbrDayNames=["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];Date.monthNames=["January","February","March","April","May","June","July","August","September","October","November","December"];Date.abbrMonthNames=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];Date.firstDayOfWeek=1;Date.format="dd/mm/yyyy";Date.fullYearStart="20";(function(){function b(c,d){if(!Date.prototype[c]){Date.prototype[c]=d}}b("isLeapYear",function(){var c=this.getFullYear();return(c%4==0&&c%100!=0)||c%400==0});b("isWeekend",function(){return this.getDay()==0||this.getDay()==6});b("isWeekDay",function(){return !this.isWeekend()});b("getDaysInMonth",function(){return[31,(this.isLeapYear()?29:28),31,30,31,30,31,31,30,31,30,31][this.getMonth()]});b("getDayName",function(c){return c?Date.abbrDayNames[this.getDay()]:Date.dayNames[this.getDay()]});b("getMonthName",function(c){return c?Date.abbrMonthNames[this.getMonth()]:Date.monthNames[this.getMonth()]});b("getDayOfYear",function(){var c=new Date("1/1/"+this.getFullYear());return Math.floor((this.getTime()-c.getTime())/86400000)});b("getWeekOfYear",function(){return Math.ceil(this.getDayOfYear()/7)});b("setDayOfYear",function(c){this.setMonth(0);this.setDate(c);return this});b("addYears",function(c){this.setFullYear(this.getFullYear()+c);return this});b("addMonths",function(d){var c=this.getDate();this.setMonth(this.getMonth()+d);if(c>this.getDate()){this.addDays(-this.getDate())}return this});b("addDays",function(c){this.setTime(this.getTime()+(c*86400000));return this});b("addHours",function(c){this.setHours(this.getHours()+c);return this});b("addMinutes",function(c){this.setMinutes(this.getMinutes()+c);return this});b("addSeconds",function(c){this.setSeconds(this.getSeconds()+c);return this});b("zeroTime",function(){this.setMilliseconds(0);this.setSeconds(0);this.setMinutes(0);this.setHours(0);return this});b("asString",function(d){var c=d||Date.format;if(c.split("mm").length>1){c=c.split("mmmm").join(this.getMonthName(false)).split("mmm").join(this.getMonthName(true)).split("mm").join(a(this.getMonth()+1))}else{c=c.split("m").join(this.getMonth()+1)}c=c.split("yyyy").join(this.getFullYear()).split("yy").join((this.getFullYear()+"").substring(2)).split("dd").join(a(this.getDate())).split("d").join(this.getDate());return c});Date.fromString=function(t){var n=Date.format;var p=new Date("01/01/1970");if(t==""){return p}t=t.toLowerCase();var m="";var e=[];var c=/(dd?d?|mm?m?|yy?yy?)+([^(m|d|y)])?/g;var k;while((k=c.exec(n))!=null){switch(k[1]){case"d":case"dd":case"m":case"mm":case"yy":case"yyyy":m+="(\\d+\\d?\\d?\\d?)+";e.push(k[1].substr(0,1));break;case"mmm":m+="([a-z]{3})";e.push("M");break}if(k[2]){m+=k[2]}}var l=new RegExp(m);var q=t.match(l);for(var h=0;h<e.length;h++){var o=q[h+1];switch(e[h]){case"d":p.setDate(o);break;case"m":p.setMonth(Number(o)-1);break;case"M":for(var g=0;g<Date.abbrMonthNames.length;g++){if(Date.abbrMonthNames[g].toLowerCase()==o){break}}p.setMonth(g);break;case"y":p.setYear(o);break}}return p};var a=function(c){var d="0"+c;return d.substring(d.length-2)}})();