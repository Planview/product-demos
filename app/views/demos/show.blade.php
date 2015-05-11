<?php
    $body_class = "demo";
?>
@extends("master.layout")

@section("title")
    {{{$demo->title}}}
@stop

@section("styles")
    @parent
    <script type="text/javascript">
    function MM_CheckFlashVersion(reqVerStr,msg){
     with(navigator){
      var isIE = (appVersion.indexOf("MSIE") != -1 && userAgent.indexOf("Opera") == -1);
      var isWin = (appVersion.toLowerCase().indexOf("win") != -1);
      if (!isIE || !isWin){ 
       var flashVer = -1;
       if (plugins && plugins.length > 0){
        var desc = plugins["Shockwave Flash"] ? plugins["Shockwave Flash"].description : "";
        desc = plugins["Shockwave Flash 2.0"] ? plugins["Shockwave Flash 2.0"].description : desc;
        if (desc == "") flashVer = -1;
        else{
         var descArr = desc.split(" ");
         var tempArrMajor = descArr[2].split(".");
         var verMajor = tempArrMajor[0];
         var tempArrMinor = (descArr[3] != "") ? descArr[3].split("r") : descArr[4].split("r");
         var verMinor = (tempArrMinor[1] > 0) ? tempArrMinor[1] : 0;
         flashVer = parseFloat(verMajor + "." + verMinor);
        }
       }
       // WebTV has Flash Player 4 or lower -- too low for video
       else if (userAgent.toLowerCase().indexOf("webtv") != -1) flashVer = 4.0;

       var verArr = reqVerStr.split(",");
       var reqVer = parseFloat(verArr[0] + "." + verArr[2]);
     
       if (flashVer < reqVer){
        if (confirm(msg))
         window.location = "http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash";
       }
      }
     } 
    }
    </script>
    <script src="/js/AC_RunActiveContent.js" type="text/javascript"></script>
@stop

@if (User::find(Auth::id())->can('manage_content'))
    @section("nav_menu_admin")
        @parent
        <li>{{HTML::link('/pvadmin/demos/'.$demo->id, 'Edit This Demo', array('id' => 'topnav-admin-view'));}}</li>
    @stop
@endif

@section("content")
    <p class="back-to-demos"><a href="/demos" title="Back to Demo List">&laquo; Back to Demo List</a></p>

    <h1 id="page-title">{{{$demo->title}}}</h1>

    <div class="col-md-8">

        <div class="limelight-video-respond">
            <span class="LimelightEmbeddedPlayer">
                {{$demo->demo_code}}
            </span>
        </div>

    </div>
    <div class="col-md-4">

        <div class="right-sidebar">
            @include('master.contact-module')

            <div class="module-related-content">
                <h2>Related Content</h2>
                <ul class="left-arrows">
                    {{$demo->related_content_code}}
                </ul>
            </div>
        </div>

    </div>
@stop
