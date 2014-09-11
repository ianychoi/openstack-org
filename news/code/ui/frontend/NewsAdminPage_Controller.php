<?php

/**
 * Class NewsPage_Controller
 */
final class NewsAdminPage_Controller extends Page_Controller {

	/**
	 * @var array
	 */
	static $allowed_actions = array(
		'logout',
        'setArticleRank'
	);


    function init() {
        parent::init();

        Requirements::css(Director::protocol()."code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css");
        Requirements::css('news/code/ui/frontend/css/news.admin.css');

        Requirements::javascript(Director::protocol()."code.jquery.com/ui/1.10.4/jquery-ui.min.js");
        Requirements::javascript('news/code/ui/frontend/js/news.admin.js');
    }

	public function __construct(){
		parent::__construct();
		$this->news_repository = new SapphireNewsRepository();
	}

	public function logout(){
		$current_member = Member::currentUser();
		if($current_member){
			$current_member->logOut();
			return Controller::curr()->redirect("Security/login?BackURL=" . urlencode($_SERVER['HTTP_REFERER']));
		}
		return Controller::curr()->redirectBack();
	}

    public function index(){

        $recent_news = $this->news_repository->getRecentNews();
        $standby_news = $this->news_repository->getStandByNews();

        return $this->renderWith(array('NewsAdminPage','Page'), array('RecentNews' => new ComponentSet($recent_news),
                                                                      'StandByNews' => new ComponentSet($standby_news)));
    }

    function NewsManagerMember()
    {
        $MemberID = Member::currentUserID();
        $currentMember = DataObject::get_one("Member", "`ID` = '" . $MemberID . "'");

        // see if the member is in the foundation group
        //if ($currentMember && $currentMember->inGroup('foundation-members')) return TRUE;

        return TRUE;
    }

    function CompanyAdmin()
    {
        return Member::currentUser()->getManagedCompanies();
    }

    public function getSliderNews() {
        $output = '';
        $counter = 0;
        $slide_news = $this->news_repository->getSlideNews();

        foreach ($slide_news as $slide_article) {
            $counter++;
            $data = array('Id'=>$slide_article->Id,'Rank'=>$slide_article->Rank,'Link'=>$slide_article->Link,
                          'Image'=>$slide_article->Image,'Headline'=>$slide_article->Headline,'Summary'=>$slide_article->Summary);
            $output .= $slide_article->renderWith('NewsAdminPage_slider', $data);
        }

        for ($i=0;$i<(5-$counter);$i++) {
            $output .= '<li class="placeholder_empty">Drop<br> here</li>';
        }

        return $output;
    }

    public function getFeaturedNews() {
        $output = '';
        $counter = 0;
        $featured_news = $this->news_repository->getFeaturedNews();

        foreach ($featured_news as $featured_article) {
            $counter++;
            $data = array('Id'=>$featured_article->Id,'Rank'=>$featured_article->Rank,'Link'=>$featured_article->Link,
                          'Image'=>$featured_article->Image,'Headline'=>$featured_article->Headline,'Summary'=>$featured_article->Summary);
            $output .= $featured_article->renderWith('NewsAdminPage_featured', $data);
        }

        for ($i=0;$i<(6-$counter);$i++) {
            $output .= '<li class="placeholder_empty">Drop<br> here</li>';
        }

        return $output;
    }

    public function setArticleRank() {
        $article_id = intval($this->request->postVar('id'));
        $old_rank = intval($this->request->postVar('old_rank'));
        $new_rank = intval($this->request->postVar('new_rank'));
        $type = $this->request->postVar('type');
        $target = $this->request->postVar('target');
        $is_new = $this->request->postVar('is_new');

        if ($is_new == 1) {
            // new item coming in, add and reorder
            $this->news_repository->setArticle($article_id,$new_rank,$target);
            $this->news_repository->sortArticle($article_id,$new_rank,$old_rank,true,false,$target);
        } elseif ($type == $target) {
            //sorting within section, reorder
            $this->news_repository->sortArticle($article_id,$new_rank,$old_rank,false,false,$type);
        } else {
            //item removed, reorder
            $this->news_repository->sortArticle($article_id,$new_rank,$old_rank,false,true,$type);
        }


    }

} 