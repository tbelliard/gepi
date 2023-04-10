//>>built
define("dojox/form/Rating",["dojo/_base/declare","dojo/_base/lang","dojo/dom-attr","dojo/dom-class","dojo/mouse","dojo/on","dojo/string","dojo/query","dijit/form/_FormWidget"],function(_1,_2,_3,_4,_5,on,_6,_7,_8){
return _1("dojox.form.Rating",_8,{templateString:null,numStars:3,value:0,buildRendering:function(_9){
this.name=this.name||"rating-"+Math.random().toString(36).substring(2);
var _a="<label class=\"dojoxRatingStar dijitInline ${hidden}\">"+"<span class=\"dojoxRatingLabel\">${value} stars</span>"+"<input type=\"radio\" name=\""+this.name+"\" value=\"${value}\" dojoAttachPoint=\"focusNode\" class=\"dojoxRatingInput\">"+"</label>";
var _b="<div dojoAttachPoint=\"domNode\" class=\"dojoxRating dijitInline\">"+"<div data-dojo-attach-point=\"list\">"+_6.substitute(_a,{value:0,hidden:"dojoxRatingHidden"})+"${stars}"+"</div></div>";
var _c="";
for(var i=0;i<this.numStars;i++){
_c+=_6.substitute(_a,{value:i+1,hidden:""});
}
this.templateString=_6.substitute(_b,{stars:_c});
this.inherited(arguments);
},postCreate:function(){
this.inherited(arguments);
this._renderStars(this.value);
this.own(on(this.list,on.selector(".dojoxRatingStar","mouseover"),_2.hitch(this,"_onMouse")),on(this.list,on.selector(".dojoxRatingStar","click"),_2.hitch(this,"_onClick")),on(this.list,on.selector(".dojoxRatingInput","change"),_2.hitch(this,"onStarChange")),on(this.list,_5.leave,_2.hitch(this,function(){
this._renderStars(this.value);
})));
},_onMouse:function(_d){
var _e=+_3.get(_d.target.querySelector("input"),"value");
this._renderStars(_e,true);
this.onMouseOver(_d,_e);
},_onClick:function(_f){
if(_f.target.tagName==="LABEL"){
var _10=+_3.get(_f.target.querySelector("input"),"value");
_f.target.value=_10;
this.onStarClick(_f,_10);
if(_10==this.value){
_f.preventDefault();
this.onStarChange(_f);
}
}
},_renderStars:function(_11,_12){
_7(".dojoxRatingStar",this.domNode).forEach(function(_13,i){
if(i>_11){
_4.remove(_13,"dojoxRatingStarHover");
_4.remove(_13,"dojoxRatingStarChecked");
}else{
_4.remove(_13,"dojoxRatingStar"+(_12?"Checked":"Hover"));
_4.add(_13,"dojoxRatingStar"+(_12?"Hover":"Checked"));
}
});
},onStarChange:function(evt){
var _14=+_3.get(evt.target,"value");
this.setAttribute("value",_14==this.value?0:_14);
this._renderStars(this.value);
this.onChange(this.value);
},onStarClick:function(evt,_15){
},onMouseOver:function(){
},setAttribute:function(key,_16){
this.set(key,_16);
},_setValueAttr:function(val){
this._set("value",val);
this._renderStars(val);
var _17=_7("input[type=radio]",this.domNode)[val];
if(_17){
_17.checked=true;
}
this.onChange(val);
}});
});
