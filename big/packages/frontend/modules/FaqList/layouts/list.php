<section id="blog" class="container">
     <div class="row">
      <div class="col-sm-12">
          <ol class="breadcrumb">
            <li><a href=""><span class="glyphicon glyphicon-home" aria-hidden="true"></span></a></li>
            <li><a href="faq.html">Hỏi đáp</a></li>
          </ol>
      </div>
      <div class="col-md-12 faq-list-bound">
          <div class="title">
              <h1>[[.FAQ.]]</h1>
          </div>
          <div class="faq-list-content">
            <!--IF:cond_item([[=faqs=]])-->
            <?php $i=1; ?>
            <!--LIST:faqs-->
                <div class="faq-list-item">
                    <h3><img src="assets/standard/images/question.gif" alt="FAQ"> <?php echo strip_tags([[=faqs.name=]]); ?></h3>
                    <div class="faq-list-answer" id="answer_[[|faqs.id|]]"><?php echo strip_tags([[=faqs.description=]]); ?></div>
                </div>
            <!--/LIST:faqs-->
            <div class="faq-list-paging">[[|paging|]]</div>
            <!--ELSE-->
            <div class="notice">[[.data_is_updating.]]</div>
            <!--/IF:cond_item-->
          </div>
      </div>
   </div>
</section>     