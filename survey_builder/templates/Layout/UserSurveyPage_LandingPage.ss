<style>
    .hero-survey {
        background-color: transparent;
        background-image: url('assets/survey/Crowd-General-Session-3.jpg');
        background-repeat: no-repeat;
        background-position: left center;
        background-attachment: scroll;
        background-size: 100%;
        height: 300px;
        border-radius: 15px;
        -webkit-box-shadow: 8px 10px 5px 1px rgba(0, 0, 0, 0.4);
        -moz-box-shadow: 8px 10px 5px 1px rgba(0, 0, 0, 0.4);
        box-shadow: 8px 10px 5px 1px rgba(0, 0, 0, 0.4);
        margin-bottom: 25px;
    }
</style>
<div id="language-selector"></div>
<div class="container">

    
    <h1>$_T("survey_ui", "Get Started")</h1>
    <% if not $Top.SurveyTemplate.isVoid && not $CurrentMember %>
        <div class="row">
            <div class="col-lg-6">
                <h3>$_T("survey_ui", "Already have an OpenStack Foundation login?")</h3>
                <div class="survey-login-wrapper">
                    <form id="MemberLoginForm_LoginForm" action="Security/login?BackURL={$Link}" method="post"
                          enctype="application/x-www-form-urlencoded">
                        <div class="Actions">
                            <input class="action" id="MemberLoginForm_LoginForm_action_dologin" type="submit"
                                   name="action_dologin" value="{$_T('survey_ui','Log In')}" title="{$_T('survey_ui','Log In')}">
                            <p id="ForgotPassword"><a href="Security/lostpassword">$_T("survey_ui", "I've lost my password")</a></p>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <h3>$_T("survey_ui", "Don't have a login? Start here.")</h3>
                <div class="survey-login-wrapper">
                    $RegisterForm
                </div>
            </div>
        </div>
    <% else %>
     <div class="row">
            <div class="col-lg-12" style="text-align: center">
                <a href="$Top.Link" title="Start Survey!" class="roundedButton">$_T("survey_ui", "Start Survey!")</a>
            </div>
        </div>
    <% end_if %>
    <!-- end - user survey report -->
    <% if $LoginPageSlide1Content && $LoginPageSlide2Content && $LoginPageSlide3Content %>
    <hr/>
    <div class="row">

        <div class="col-lg-4">
            <div id="user">
                <p>$_T("survey_ui", $LoginPageSlide1Content)</p>
            </div>
        </div>

        <div class="col-lg-4">
            <div id="time">
                <p>$_T("survey_ui", $LoginPageSlide2Content)</p>
            </div>
        </div>

        <div class="survey-box col-lg-4">
            <div id="private">
                <p>$_T("survey_ui", $LoginPageSlide3Content)</p>
            </div>
        </div>
    </div>
    <% end_if %>
    <p>$_T("survey_ui", "This survey provides users an opportunity to influence the community and software direction. By sharing information about your configuration and requirements, the OpenStack Foundation User Committee will be able to advocate on your behalf.")</p>
    <p><a href="/user-survey/faq" class="roundedButton">$_T("survey_ui", "More Information About The Survey")</a></p>
    <hr />


    <!-- user survey report -->
    <p>&nbsp;</p>
    <div class="row">
        <div class="col-lg-12">
            <div class="condensed hero-survey">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <h1 style="color: white">$_T("survey_ui", "See the results from the latest User Survey")</h1>
                        </div>
                    </div>
                    <div title="Photo by the OpenStack Foundation" data-placement="left" data-toggle="tooltip"
                         class="hero-credit" data-original-title="Photo by the OpenStack Foundation">
                        <a target="_blank" href="#"><i class="fa fa-info-circle"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h3>$_T("survey_ui", "See the OpenStack community’s opinions, adoption and technology choices")</h3>
            <p>
                $_T("survey_ui", "Read more from the ninth survey of OpenStack users since April 2013, with a goal of better understanding user perspectives, organizational profiles, use cases, and technology choices across the community’s deployments. This survey offers insights from more than 1,300 users, representing 44% more deployments and 22% more organizations than ever before.")
            </p>
            <p>
                <a class="roundedButton" href="https://www.openstack.org/assets/survey/April2017SurveyReport.pdf" target="_blank">$_T("survey_ui", "Read the April 2017 report")</a>
            </p>
            <h3>$_T("survey_ui", "Be your own data scientist")</h3>
            <p>
                $_T("survey_ui", "Uncover your own findings by digging into the User Survey data from the past year with a <a href=\"%s\">new analysis tool</a> available to the OpenStack community. Apply multiple filters to virtually every quantitative question from the user survey.", /analytics)
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <h3>$_T("survey_ui", "Watch a quick video overview")</h3>
            <ul class="list-unstyled">
                <li><a href="https://www.youtube.com/watch?v=1yerazN3ZHc&feature=youtu.be" target="_blank">$_T("survey_ui", "April 2017 full report")</a></li>
                <li><a href="https://www.youtube.com/watch?v=m9p8NUMs_PM&feature=youtu.be" target="_blank">$_T("survey_ui", "October 2016 highlights report")</a></li>
                <li><a href="http://www.amazon.com/dp/1532707053/" target="_blank">$_T("survey_ui", "Order a print copy of the April 2016 full report")</a></li>
                <li><a href="https://www.youtube.com/watch?v=lmu5r7BCY_U&feature=youtu.be" target="_blank">$_T("survey_ui", "April 2016 full report")</a></li>
            </ul>
            <h3>$_T("survey_ui", "See prior surveys")</h3>
            <p>
                $_T("survey_ui", "Learn more about past User Survey data to see how OpenStack is growing and maturing.")
            </p>
            <ul class="list-unstyled">
                <li><a href="https://www.openstack.org/assets/survey/October2016SurveyReport.pdf">$_T("survey_ui", "October 2016 highlights report")</a></li>
                <li><a href="https://www.openstack.org/assets/survey/April-2016-User-Survey-Report.pdf">$_T("survey_ui", "April 2016 full report")</a></li>
                <li>
                    <a href="http://www.openstack.org/assets/survey/Public-User-Survey-Report.pdf">$_T("survey_ui", "October 2015 Full report")</a>
                <li>
                    <a href="http://superuser.openstack.org/articles/user-survey-identifies-leading-industries-and-business-drivers-for-openstack-adoption"
                       target="_blank">$_T("survey_ui", "May 2015 Demographics")</a></li>
                <li>
                    <a href="http://superuser.openstack.org/articles/user-survey-identifies-leading-industries-and-business-drivers-for-openstack-adoption"
                       target="_blank">$_T("survey_ui", "May 2015 Business drivers")</a></li>
                <li>
                    <a href="http://superuser.openstack.org/articles/openstack-users-share-how-their-deployments-stack-up"
                       target="_blank">$_T("survey_ui", "May 2015 Deployments")</a></li>
                <li><a href="http://superuser.openstack.org/articles/openstack-user-survey-insights-november-2014"
                       target="_blank">$_T("survey_ui", "November 2014 Full report")</a></li>
            </ul>
            <h3>$_T("survey_ui", "Order the User Survey as a printed book")</h3>
            <ul class="list-unstyled">
                <li>$_T("survey_ui", "April 2017 full report (coming soon)")</li>
                <li><a href="https://www.amazon.com/dp/1532707053/" target="_blank">$_T("survey_ui", "April 2016 full report")</a></li>
            </ul>
        </div>
        <div class="col-sm-4" style="padding-top:15px;">
            <a href="https://www.youtube.com/watch?v=1yerazN3ZHc&feature=youtu.be" target="_blank"><img src="https://www.openstack.org/assets/survey/usersurvey-video.jpg"></a>
        </div>
    </div>
    <script>
        $(function () {
            var param = $('#fragment');
            param.val(window.location.hash);
        });
    </script>
</div>
$ModuleJS('survey')
$ModuleCSS('survey')