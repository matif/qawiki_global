


<div class="qaw-dlg-content">

  <div class="qaw-contributor-header"><?php echo $config['functions']['header']['text'] ?></div>
  <div class="qaw-clear"></div>
  
  <div class="qaw-cont-user-detail qaw-clearfix">
    <div class="qaw-user-avatar"></div>
    <div class="qaw-glob-heading qaw-cont-heading"><?php echo parse_visual_tags($config, 'title', array('#UserName' => $contributor['name']))?>,</div>
    <div class="qaw-cont-date-panel">
      <ul>
        <li>Joined At: <?php echo date('M j, Y', strtotime($contributor['created']))?></li>
        <li>Vote Score: <?php echo $contributor['thumbs_up']?></li>
      </ul>
    </div>
  </div>
  
  <div class="qaw-clear"></div>
  
  <div class="qaw-cont-question-panel">
    <h3>Questions</h3>
    <ul>
      
      <?php if (isset($details['questions'])) : ?>
      
        <?php foreach ($details['questions'] as $question) : ?>

          <li><?php echo parse_question_tags($question, $question['qa_title'].' #Date/Time')?></li>
        
        <?php endforeach; ?>
        
      <?php endif; ?>
        
    </ul>
  </div>						
  <div class="qaw-cont-question-panel">
    <h3>Answers</h3>
    <ul>
      
      <?php if (isset($details['answers'])) : ?>
      
        <?php foreach ($details['answers'] as $answer) : ?>
      
          <li><?php echo parse_question_tags($answer, $answer['qa_title'].' #Date/Time')?></li>
        
        <?php endforeach; ?>
        
      <?php endif; ?>
      
    </ul>
  </div>

  <div>
    <a id="qaw-contributor-close" class="<?php echo (strpos($config['functions']['contributor']['class'], 'custom') !== FALSE ? 'qaw-custom-btn ' : 'qaw-button ').$config['functions']['contributor']['class']?>" href="javascript:;" onclick="qaw_widget.qawiki_html.remove_dialog()">
      <span><?php echo $config['functions']['contributor']['text'] ?></span>
    </a>
  </div>

  <div class="qaw-clear"></div>

</div>