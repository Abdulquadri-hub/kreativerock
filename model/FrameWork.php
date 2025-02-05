<?php
class FrameWork{

    public $model;
    
    public function __construct(){
        $this->model = new Model();
    }

    /** Logos ***************************************************************************/
    
    public function LogoInfo($condition){
        return $this->model->findOne("logos", $condition);
    }
    public function checkIfLogoExists($condition){
        return count($this->model->findOne("logos", $condition)) > 0 ? true : false;
    }
    public function registerLogo($fields, $values){
        return $this->model->insertdata("logos", $fields, $values);
    }
    public function retrieveAllLogos($pageno, $limit){
        $data = $this->model->paginate("logos", " 1 ORDER BY location ASC", $pageno, $limit);
        return $data;
    }
    public function retrieveLogoByStatus($status, $pageno, $limit){
        $data = $this->model->paginate("logos", "status LIKE '$status' ORDER BY id ASC", $pageno, $limit);
        return $data;
    }    
    public function retrieveLogoByQuery($query, $pageno, $limit,$field = "*"){
        $data = $this->model->paginate("logos", $query, $pageno, $limit,$field);
        return $data;
    }    
    public function updateLogoDetails($query, $id){
        return $this->model->update('logos', $query, "WHERE id = $id");
    }    
    /** End of Logos *****************************************************************************************************/
    
    /** Navigation ******************************************************************************************************/
    public function getNavigationInfo($condition){
        return $this->model->findOne("navigation", $condition);
    }
    public function checkIfNavigationExists($condition){
        return count($this->model->findOne("navigation", $condition)) > 0 ? true : false;
    }
    public function registerNavigation($fields, $values){
        return $this->model->insertdata("navigation", $fields, $values);
    }
    public function updateNavigationDetails($query, $id){
        return $this->model->update('navigation', $query, "WHERE id = $id");
    }    
    /** End of Navigation ************************************************************************************************/    

    /** Banner ******************************************************************************************************/
    public function getBannerInfo($condition){
        return $this->model->findOne("banner", $condition);
    }
    public function checkIfBannerExists($condition){
        return count($this->model->findOne("banner", $condition)) > 0 ? true : false;
    }
    public function registerBanner($fields, $values){
        return $this->model->insertdata("banner", $fields, $values);
    }
    public function updateBannerDetails($query, $id){
        return $this->model->update('banner', $query, "WHERE id = $id");
    }    
    /** End of Banner ************************************************************************************************/    

    /** Seat Category ******************************************************************************************************/
    public function getSeatCategoryInfo($condition){
        return $this->model->findOne("seatcategory", $condition);
    }
    public function checkIfSeatCategoryExists($condition){
        return count($this->model->findOne("seatcategory", $condition)) > 0 ? true : false;
    }
    public function registerSeatCategory($fields, $values){
        return $this->model->insertdata("seatcategory", $fields, $values);
    }
    public function updateSeatCategoryDetails($query, $id){
        return $this->model->update('seatcategory', $query, "WHERE id = $id");
    }    
    /** End of Seat Category ************************************************************************************************/    

    /** Other Info ******************************************************************************************************/
    public function getOtherInfo($condition){
        return $this->model->findOne("otherinfo", $condition);
    }
    public function checkIfOtherInfoExists($condition){
        return count($this->model->findOne("otherinfo", $condition)) > 0 ? true : false;
    }
    public function registerOtherInfo($fields, $values){
        return $this->model->insertdata("otherinfo", $fields, $values);
    }
    public function updateOtherInfoDetails($query, $id){
        return $this->model->update('otherinfo', $query, "WHERE id = $id");
    }    
    /** End of Other Info ************************************************************************************************/    

    /** Tab Promotion ******************************************************************************************************/
    public function getTabPromotionInfo($condition){
        return $this->model->findOne("tabpromotion", $condition);
    }
    public function checkIfTabPromotionExists($condition){
        return count($this->model->findOne("tabpromotion", $condition)) > 0 ? true : false;
    }
    public function registerTabPromotion($fields, $values){
        return $this->model->insertdata("tabpromotion", $fields, $values);
    }
    public function updateTabPromotionDetails($query, $id){
        return $this->model->update('tabpromotion', $query, "WHERE id = $id");
    }    
    /** End of Tab Promotion ************************************************************************************************/    


    /** Ad Slider ******************************************************************************************************/
    public function getAdSliderInfo($condition){
        return $this->model->findOne("adslider", $condition);
    }
    public function checkIfAdSliderExists($condition){
        return count($this->model->findOne("adslider", $condition)) > 0 ? true : false;
    }
    public function registerAdSlider($fields, $values){
        return $this->model->insertdata("adslider", $fields, $values);
    }
    public function updateAdSliderDetails($query, $id){
        return $this->model->update('adslider', $query, "WHERE id = $id");
    }    
    /** End of Ad Slider ************************************************************************************************/    


    /** Trending Flight Deals ******************************************************************************************************/
    public function getTrendingFlightDealsInfo($condition){
        return $this->model->findOne("trendingflightdeals", $condition);
    }
    public function checkIfTrendingFlightDealsExists($condition){
        return count($this->model->findOne("trendingflightdeals", $condition)) > 0 ? true : false;
    }
    public function registerTrendingFlightDeals($fields, $values){
        return $this->model->insertdata("trendingflightdeals", $fields, $values);
    }
    public function updateTrendingFlightDealsDetails($query, $id){
        return $this->model->update('trendingflightdeals', $query, "WHERE id = $id");
    }    
    /** End of Trending Flight Deals ************************************************************************************************/    

    /** Popular Hotels ******************************************************************************************************/
    public function getPopularHotelsInfo($condition){
        return $this->model->findOne("popularhotels", $condition);
    }
    public function checkIfPopularHotelsExists($condition){
        return count($this->model->findOne("popularhotels", $condition)) > 0 ? true : false;
    }
    public function registerPopularHotels($fields, $values){
        return $this->model->insertdata("popularhotels", $fields, $values);
    }
    public function updatePopularHotelsDetails($query, $id){
        return $this->model->update('popularhotels', $query, "WHERE id = $id");
    }    
    /** End of Popular Hotels ************************************************************************************************/    

    /** Static Ad Banner ******************************************************************************************************/
    public function getStaticAdBannerInfo($condition){
        return $this->model->findOne("staticadbanner", $condition);
    }
    public function checkIfStaticAdBannerExists($condition){
        return count($this->model->findOne("staticadbanner", $condition)) > 0 ? true : false;
    }
    public function registerStaticAdBanner($fields, $values){
        return $this->model->insertdata("staticadbanner", $fields, $values);
    }
    public function updateStaticAdBannerDetails($query, $id){
        return $this->model->update('staticadbanner', $query, "WHERE id = $id");
    }    
    /** End of Static Ad Banner ************************************************************************************************/    

    /** Partners *********************************************************************************************************/
    public function getPartnersInfo($condition){
        return $this->model->findOne("partners", $condition);
    }
    public function checkIfPartnersExists($condition){
        return count($this->model->findOne("partners", $condition)) > 0 ? true : false;
    }
    public function registerPartners($fields, $values){
        return $this->model->insertdata("partners", $fields, $values);
    }
    public function updatePartnersDetails($query, $id){
        return $this->model->update('partners', $query, "WHERE id = $id");
    }    
    /** End of Partners ************************************************************************************************/    

    /** Newsletter *********************************************************************************************************/
    public function getNewsLetterInfo($condition){
        return $this->model->findOne("newsletter", $condition);
    }
    public function checkIfNewsLetterExists($condition){
        return count($this->model->findOne("newsletter", $condition)) > 0 ? true : false;
    }
    public function registerNewsLetter($fields, $values){
        return $this->model->insertdata("newsletter", $fields, $values);
    }
    public function updateNewsLetterDetails($query, $id){
        return $this->model->update('newsletter', $query, "WHERE id = $id");
    }    
    /** End of Newsletter ************************************************************************************************/    

    /** Visa Countries *********************************************************************************************************/
    public function getVisaCountriesInfo($condition){
        return $this->model->findOne("visacountries", $condition);
    }
    public function checkIfVisaCountriesExists($condition){
        return count($this->model->findOne("visacountries", $condition)) > 0 ? true : false;
    }
    public function registerVisaCountries($fields, $values){
        return $this->model->insertdata("visacountries", $fields, $values);
    }
    public function updateVisaCountriesDetails($query, $id){
        return $this->model->update('visacountries', $query, "WHERE id = $id");
    }    
    /** End of Visa Countries ************************************************************************************************/    


    /** Footer Quick Links *********************************************************************************************************/
    public function getFooterQuickLinksInfo($condition){
        return $this->model->findOne("footerquicklinks", $condition);
    }
    public function checkIfFooterQuickLinksExists($condition){
        return count($this->model->findOne("footerquicklinks", $condition)) > 0 ? true : false;
    }
    public function registerFooterQuickLinks($fields, $values){
        return $this->model->insertdata("footerquicklinks", $fields, $values);
    }
    public function updateFooterQuickLinksDetails($query, $id){
        return $this->model->update('footerquicklinks', $query, "WHERE id = $id");
    }    
    /** End of Footer Quick Links ************************************************************************************************/    

    /** Social Media Links *********************************************************************************************************/
    public function getSocialMediaLinksInfo($condition){
        return $this->model->findOne("socialmedialinks", $condition);
    }
    public function checkIfSocialMediaLinksExists($condition){
        return count($this->model->findOne("socialmedialinks", $condition)) > 0 ? true : false;
    }
    public function registerSocialMediaLinks($fields, $values){
        return $this->model->insertdata("socialmedialinks", $fields, $values);
    }
    public function updateSocialMediaLinksDetails($query, $id){
        return $this->model->update('socialmedialinks', $query, "WHERE id = $id");
    }    
    /** End of Social Media Links ************************************************************************************************/    

    /** Copyright ******************************************************************************************************************/
    public function getCopyRightInfo($condition){
        return $this->model->findOne("copyright", $condition);
    }
    public function checkIfCopyRightExists($condition){
        return count($this->model->findOne("copyright", $condition)) > 0 ? true : false;
    }
    public function registerCopyRight($fields, $values){
        return $this->model->insertdata("copyright", $fields, $values);
    }
    public function updateCopyRightDetails($query, $id){
        return $this->model->update('copyright', $query, "WHERE id = $id");
    }    
    /** End of Copyright ***********************************************************************************************************/    

    /** Search Flights & Hotels ******************************************************************************************************************/
    public function getSearchFlightHotelsInfo($condition){
        return $this->model->findOne("searchflighthotels", $condition);
    }
    public function checkIfSearchFlightHotelsExists($condition){
        return count($this->model->findOne("searchflighthotels", $condition)) > 0 ? true : false;
    }
    public function registerSearchFlightHotels($fields, $values){
        return $this->model->insertdata("searchflighthotels", $fields, $values);
    }
    public function updateSearchFlightHotelsDetails($query, $id){
        return $this->model->update('searchflighthotels', $query, "WHERE id = $id");
    }    
    /** End of Search Flights & Hotels ***********************************************************************************************************/    

    /** FAQs ******************************************************************************************************************/
    public function getFAQInfo($condition){
        return $this->model->findOne("faqs", $condition);
    }
    public function registerFAQ($fields, $values){
        return $this->model->insertdata("faqs", $fields, $values);
    }
    public function updateFAQDetails($query, $id){
        return $this->model->update('faqs', $query, "WHERE id = $id");
    }    
    /** End of FAQs ***********************************************************************************************************/    

    /** Blogs Categries ******************************************************************************************************************/
    public function getBlogCategoriesInfo($condition){
        return $this->model->findOne("blogcategories", $condition);
    }
    public function registerBlogCategories($fields, $values){
        return $this->model->insertdata("blogcategories", $fields, $values);
    }
    public function updateBlogCategoriesDetails($query, $id){
        return $this->model->update('blogcategories', $query, "WHERE id = $id");
    }    
    /** End of Blogs Categories ***********************************************************************************************************/    

    /** Blogs ******************************************************************************************************************/
    public function getBlogInfo($condition){
        return $this->model->findOne("blogpost", $condition);
    }
    public function registerBlog($fields, $values){
        return $this->model->insertdata("blogpost", $fields, $values);
    }
    public function updateBlogDetails($query, $id){
        return $this->model->update('blogpost', $query, "WHERE id = $id");
    }    
    /** End of Blogs ***********************************************************************************************************/    

    /** Organisation ******************************************************************************************************************/
    public function getOrganisationInfo($condition){
        return $this->model->findOne("organisation", $condition);
    }
    public function registerOrganisation($fields, $values){
        return $this->model->insertdata("organisation", $fields, $values);
    }
    public function updateOrganisationDetails($query, $id){
        return $this->model->update('organisation', $query, "WHERE id = $id");
    }    
    /** End of Organisation ***********************************************************************************************************/    

    /** Trivia Test ******************************************************************************************************************/
    public function getTriviaTestInfo($condition){
        return $this->model->findOne("triviacontent", $condition);
    }
    public function registerTriviaTest($fields, $values){
        $result = $this->model->insertdata("triviacontent", $fields, $values);
        if($result){
            return $this->model->lastId();
        }else{
            return null;
        }                  
    }
    public function updateTriviaTestDetails($query, $id){
        return $this->model->update('triviacontent', $query, "WHERE id = $id");
    }    
    /** End of Trivia Test ***********************************************************************************************************/    

    /** Trivia QS ******************************************************************************************************************/
    public function getTriviaQSInfo($condition){
        return $this->model->findOne("triviaqs", $condition);
    }
    public function registerTriviaQS($fields, $values){
        return $this->model->insertdata("triviaqs", $fields, $values);
    }
    public function updateTriviaQSDetails($query, $id){
        return $this->model->update('triviaqs', $query, "WHERE id = $id");
    }    
    /** End of Trivia QS ***********************************************************************************************************/    

    /** Contest ******************************************************************************************************************/
    public function getContestInfo($condition){
        return $this->model->findOne("contest", $condition);
    }
    public function registerContest($fields, $values){
        $result = $this->model->insertdata("contest", $fields, $values);
        if($result){
            return $this->model->lastId();
        }else{
            return null;
        }            
    }
    public function updateContestDetails($query, $id){
        return $this->model->update('contest', $query, "WHERE id = $id");
    }    
    /** End of Contest ***********************************************************************************************************/    
    
    /** Candidates ******************************************************************************************************************/
    public function getCandidateInfo($condition){
        return $this->model->findOne("candidates", $condition);
    }
    public function registerCandidate($fields, $values){
        return $this->model->insertdata("candidates", $fields, $values);
    }
    public function updateCandidateDetails($query, $id){
        return $this->model->update('candidates', $query, "WHERE id = $id");
    }    
    /** End of Candidates ***********************************************************************************************************/    
    
    /** Voting Package ******************************************************************************************************************/
    public function getVotingPackageInfo($condition){
        return $this->model->findOne("votingpackage", $condition);
    }
    public function registerVotingPackage($fields, $values){
        return $this->model->insertdata("votingpackage", $fields, $values);
    }
    public function updateVotingPackageDetails($query, $id){
        return $this->model->update('votingpackage', $query, "WHERE id = $id");
    }    
    /** End of Voting Package ***********************************************************************************************************/    
    
    /** Contest Category ******************************************************************************************************************/
    public function getContestCategoryInfo($condition){
        return $this->model->findOne("contestcategory", $condition);
    }
    public function registerContestCategory($fields, $values){
        return $this->model->insertdata("contestcategory", $fields, $values);
    }
    public function updateContestCategoryDetails($query, $id){
        return $this->model->update('contestcategory', $query, "WHERE id = $id");
    }    
    /** End of Contest Category ***********************************************************************************************************/    
    
    /** Sponsors ******************************************************************************************************************/
    public function getSponsorInfo($condition){
        return $this->model->findOne("sponsors", $condition);
    }
    public function registerSponsor($fields, $values){
        return $this->model->insertdata("sponsors", $fields, $values);
    }
    public function updateSponsorDetails($query, $id){
        return $this->model->update('sponsors', $query, "WHERE id = $id");
    }    
    /** End of Sponsors ***********************************************************************************************************/    
    
    /** Blog Comment ******************************************************************************************************************/
    public function getBlogCommentInfo($condition){
        return $this->model->findOne("blogcomment", $condition);
    }
    public function registerBlogComment($fields, $values){
        return $this->model->insertdata("blogcomment", $fields, $values);
    }
    public function updateBlogCommentDetails($query, $id){
        return $this->model->update('blogcomment', $query, "WHERE id = $id");
    }    
    /** End of Blog Comments ***********************************************************************************************************/    

    /** Event Ticket Category ******************************************************************************************************************/
    public function getEventTicketCategoriesInfo($condition){
        return $this->model->findOne("eventticketcategory", $condition);
    }
    public function registerEventTicketCategories($fields, $values){
        return $this->model->insertdata("eventticketcategory", $fields, $values);
    }
    public function updateEventTicketCategoriesDetails($query, $id){
        return $this->model->update('eventticketcategory', $query, "WHERE id = $id");
    }    
    /** End of Event Ticket Category ***********************************************************************************************************/    

    /** More Event Ticket ******************************************************************************************************************/
    public function getMoreEventTicketInfo($condition){
        return $this->model->findOne("moreventdata", $condition);
    }
    public function registerMoreEventTicket($fields, $values){
        $result = $this->model->insertdata("moreventdata", $fields, $values);
        if($result){
            return $this->model->lastId();
        }else{
            return null;
        }                
    }
    public function updateMoreEventTicketDetails($query, $id){
        return $this->model->update('moreventdata', $query, "WHERE id = $id");
    }    
    /** End of More Event Ticket ***********************************************************************************************************/    

    /** Event Ticket ******************************************************************************************************************/
    public function getEventTicketInfo($condition){
        return $this->model->findOne("eventticket", $condition);
    }
    public function registerEventTicket($fields, $values){
        return $this->model->insertdata("eventticket", $fields, $values);
    }
    public function updateEventTicketDetails($query, $id){
        return $this->model->update('eventticket', $query, "WHERE id = $id");
    }    
    /** End of Event Ticket ***********************************************************************************************************/    
    
    /** Trivia Answers ******************************************************************************************************************/
    public function getTestAnswerInfo($condition){
        return $this->model->findOne("triviatestanswers", $condition);
    }
    public function registerTestAnswer($fields, $values){
        return $this->model->insertdata("triviatestanswers", $fields, $values);
    }
    public function updateTestAnswerDetails($query, $id){
        return $this->model->update('triviatestanswers', $query, "WHERE id = $id");
    }    
    /** End of Trivia Answers ***********************************************************************************************************/    
    
    /** Services ******************************************************************************************************************/
    public function getServiceInfo($condition){
        return $this->model->findOne("services", $condition);
    }
    public function registerService($fields, $values){
        $result = $this->model->insertdata("services", $fields, $values);
        if($result){
            return $this->model->lastId();
        }else{
            return null;
        }             
    }
    public function updateServiceDetails($query, $id){
        return $this->model->update('services', $query, "WHERE id = $id");
    }    
    /** End of Services ***********************************************************************************************************/    
    
    /** Vendor Event ******************************************************************************************************************/
    public function getVendorEventInfo($condition){
        return $this->model->findOne("vendorevent", $condition);
    }
    public function registerVendorEvent($fields, $values){
        return $this->model->insertdata("vendorevent", $fields, $values);
    }
    public function updateVendorEventDetails($query, $id){
        return $this->model->update('vendorevent', $query, "WHERE id = $id");
    }    
    /** End of Vendor Event ***********************************************************************************************************/    

    /** Vendor Bids ******************************************************************************************************************/
    public function getBidInfo($condition){
        return $this->model->findOne("bid", $condition);
    }
    public function registerBid($fields, $values){
        return $this->model->insertdata("bid", $fields, $values);
    }
    public function updateBidDetails($query, $id){
        return $this->model->update('bid', $query, "WHERE id = $id");
    }    
    /** End of Vendor Bids ***********************************************************************************************************/    

    /** Vendor Service Package Pricing ******************************************************************************************************************/
    public function getVendorServicePricingInfo($condition){
        return $this->model->findOne("vendorservicepackagepricing", $condition);
    }
    public function registerVendorServicePricing($fields, $values){
        return $this->model->insertdata("vendorservicepackagepricing", $fields, $values);
    }
    public function updateVendorServicePricingDetails($query, $id){
        return $this->model->update('vendorservicepackagepricing', $query, "WHERE id = $id");
    }    
    /** End of Vendor Service Package Pricing*****************************************************************************************************/    
    
    /** Tour Package ******************************************************************************************************************/
    public function getTourPackageInfo($condition){
         return $this->model->findOne("tourpackage", $condition);
    }
    public function registerTourPackage($fields, $values){
        $result = $this->model->insertdata("tourpackage", $fields, $values);
        if($result){
            return $this->model->lastId();
        }else{
            return null;
        }              
    }
    public function updateTourPackageDetails($query, $id){
        return $this->model->update('tourpackage', $query, "WHERE id = $id");
    }    
    /** End of TourPackage ******************************************************************************************************/    
    
    /** Tour Package Pricing***********************************************************************************************************/
    public function getTourPackagePricingInfo($condition){
        return $this->model->findOne("tourpackagepricing", $condition);
    }
    public function registerTourPackagePricing($fields, $values){
        return $this->model->insertdata("tourpackagepricing", $fields, $values);
    }
    public function updateTourPackagePricingDetails($query, $id){
        return $this->model->update('tourpackagepricing', $query, "WHERE id = $id");
    }    
    /** End of Tour Package Pricing ******************************************************************************************************/    
    
    /** Form Content***********************************************************************************************************/
    public function getFormContentInfo($condition){
         return $this->model->findOne("formcontent", $condition);
        
    }
    public function registerFormContent($fields, $values){
        $result = $this->model->insertdata("formcontent", $fields, $values);
        if($result){
            return $this->model->lastId();
        }else{
            return null;
        }           
    }
    public function updateFormContentDetails($query, $id){
        return $this->model->update('formcontent', $query, "WHERE id = $id");
    }    
    /** End of Form Content ******************************************************************************************************/    
    
    /** Form Inputs***********************************************************************************************************/
    public function getFormInputsInfo($condition){
        return $this->model->findOne("forminputs", $condition);
    }
    public function registerFormInputs($fields, $values){
        return $this->model->insertdata("forminputs", $fields, $values);
    }
    public function updateFormInputsDetails($query, $id){
        return $this->model->update('forminputs', $query, "WHERE id = $id");
    }    
    /** End of Form Inputs ******************************************************************************************************/    

    /** Form Answers ******************************************************************************************************************/
    public function getFormTestAnswerInfo($condition){
        return $this->model->findOne("formtestanswers", $condition);
    }
    public function registerFormTestAnswer($fields, $values){
        return $this->model->insertdata("formtestanswers", $fields, $values);
    }
    public function updateFormTestAnswerDetails($query, $id){
        return $this->model->update('formtestanswers', $query, "WHERE id = $id");
    }    
    /** End of Form Answers ***********************************************************************************************************/    
        
    /** Contest Settings ******************************************************************************************************************/
    public function getContestSettingsInfo($condition){
        return $this->model->findOne("contestsettings", $condition);
    }
    public function registerContestSettings($fields, $values){
        return $this->model->insertdata("contestsettings", $fields, $values);
    }
    public function updateContestSettingsDetails($query, $id){
        return $this->model->update('contestsettings', $query, "WHERE id = $id");
    }    
    /** End of Contest Settings ***********************************************************************************************************/    
                
    public function getRowsNumber($table){
        return count($this->model->findAll($table));
    }

    public function executeByQuerySelector($query){
        $data = $this->model->exec_query($query);
        return $data;
    }
}
?>