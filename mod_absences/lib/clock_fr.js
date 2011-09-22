/*
 * Last modification  : 18/03/2005
 *
 */


var LC_Style=["<span class='clock'>","</span>",0,1,4,1,2];
// arg 0 : balise d'ouverture
// arg 1 : balise de fermeture
// arg 2 : si 0 affichage sur 24 heures, si 1 affichage anglais AM et PM
// arg 3 : si 1 -> actualisation toutes les secondes, si 0 -> pas d'actualisation
// arg 4 : différents format d'affichage de la date valeurs possibles : 1, 2, 3 et 4
// arg 5 : si 0 -> nom complet des jours et mois. Si 1 -> noms abrégés
// arg 6 : valeur ajoutée au GMT

var LC_IE=(document.all);
var LC_NS=(document.layers);
var LC_N6=(window.sidebar);
var LC_Old=(!LC_IE&&!LC_NS&&!LC_N6);
var LC_Clocks=new Array();
var LC_DaysOfWeek=[["Dimanche","Dim."],["Lundi","Lun."],["Mardi","Mar."],["Mercredi","Mer."],["Jeudi","Jeu."],["Vendredi","Ven."],["Samedi","Sam."]];
var LC_MonthsOfYear=[["Janvier","Jan."],["Fevrier","Fev."],["Mars","Mar."],["Avril","Avr."],["Mai","Mai."],["Juin","Juin."],["Juillet","Juil."],["Août","Aoû."],["Septembre","Sep."],["Octobre","Oct."],["Novembre","Nov."],["Décembre","Dec."]];
var LC_ClockUpdate=[0,1000,60000];

function LC_CreateClock(c){
    if(LC_IE||LC_N6){
        clockTags='<span id="'+c.Name+'"></span>'
    }else if(LC_NS){
        clockTags='<ilayer  id="'+c.Name+'Pos"><layer id="'+c.Name+'"></layer></ilayer>'
    }
    if(!LC_Old){
        document.write(clockTags)
    }else{
        LC_UpdateClock(LC_Clocks.length-1)
    }
}

function LC_InitializeClocks(){
    LC_OtherOnloads();
    if(LC_Old){
        return
    }
    for(i=0;i<LC_Clocks.length;i++){
        LC_UpdateClock(i);
        if(LC_Clocks[i].Update){
            eval('var '+LC_Clocks[i].Name+'=setInterval("LC_UpdateClock("+'+i+'+")",'+LC_ClockUpdate[LC_Clocks[i].Update]+')');
        }
    }
}

function LC_UpdateClock(Clock){
    var c=LC_Clocks[Clock];
    var t=new Date();
    var day=t.getDay();
    var md=t.getDate();
    var mnth=t.getMonth();
    var hrs=t.getHours();
    var mins=t.getMinutes();
    var secs=t.getSeconds();
    var yr=t.getYear();
    if(yr<1900){yr+=1900}
    if(c.DisplayDate>=3){md+="";abbrev="th";
    if(md.charAt(md.length-2)!=1){
        var tmp=md.charAt(md.length-1);
        if(tmp==1){
            abbrev="sd"
        }else if(tmp==2){
            abbrev="nd"
        }else if(tmp==3){
            abbrev="rd"
        }
    }
    }
    var ampm="";
    if(c.Hour12==1){ampm="AM";
    if(hrs>=12){ampm="PM";hrs-=12}
    if(hrs==0){hrs=12}}
    if(mins<=9){mins="0"+mins}
    if(secs<=9){secs="0"+secs}
    var html='<b>';html+=c.OpenTags;
    if(LC_NS){html+='<center>';}
    if(c.DisplayDate==1){html+=' '+md+'/'+(mnth+1)+'/'+yr}
    if(c.DisplayDate==2){html+=' '+(mnth+1)+'/'+md+'/'+yr}
    if(c.DisplayDate>=3){html+=LC_DaysOfWeek[day][c.Abbreviate]+' '+md+' '+LC_MonthsOfYear[mnth][c.Abbreviate]}
    if(c.DisplayDate>=4){html+=' '+yr}html+='</b>';
    // html+='<br />';
    html+=' - ';
    html+='<b>';
    html+=hrs+':'+mins;
    if(c.Update==1){html+=':'+secs}
    if(c.Hour12){html+=' '+ampm}html+=c.CloseTags;
    if(LC_NS){html+='</center>';}
    html+='</b>';
    if(LC_NS){
        var l=document.layers[c.Name+"Pos"].document.layers[c.Name].document;l.open();l.write(html);l.close();
    }else if(LC_N6||LC_IE){
        document.getElementById(c.Name).innerHTML=html;
    }else{document.write(html);}
}


function LiveClock(a,b,c,d,e,f,g,h,i,j,k,l){
this.Name='LiveClock'+LC_Clocks.length;
this.OpenTags=e||LC_Style[0];
this.CloseTags=f||LC_Style[1];
this.Hour12=h||LC_Style[2];
this.Update=i||LC_Style[3];
this.DisplayDate=k||LC_Style[4];
this.Abbreviate=j||LC_Style[5];
this.GMT=l||LC_Style[6];
LC_Clocks[LC_Clocks.length]=this;
LC_CreateClock(this);
}

LC_OtherOnloads=(window.onload)?window.onload:new Function;window.onload=LC_InitializeClocks;