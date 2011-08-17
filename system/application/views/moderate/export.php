<style type="text/css">
  table, td, tr{font-size:12px; font-family: Arial}
  .answers, .history{font-size:11px}
</style>

<table width="100%">
  
  <tr>
    <td height="20">&nbsp;</td>
  </tr>
  
  <tr>
    <td>
  
      <?php foreach($questions as $key => $question) :?>

        <?php $post_status = post_status($question);?>

        <table cellpadding="0" cellspacing="0" width="100%" style="border:1px solid <?php echo ($post_status == 'Approved') ? '#379200' : ($post_status == 'Rejected' ? '#F61D00' : '#F7941D')?>">
          <tr>
            <td bgcolor="#FFFDE4" align="right">
              Helpful? Yes [<?php echo ($question['pos_vote']) ? $question['pos_vote'] : 0?>] 
              No[<?php echo ($question['neg_vote']) ? $question['neg_vote'] : 0?>] 
              Flag[<?php echo $question['spam_count']?>] 
              Answers[<?php echo $question['answers_count']?>] 
            </td>
          </tr>
          <tr>                
            <td><?php echo $question['qa_title']?></td>
          </tr>
          <tr>
            <td><?php echo $question['qa_description']?></td>
          </tr>
          <tr>
            <td>Asked by <?php echo $question['user_name']?> @ <?php echo format_time($question['qa_created_at']);?></td>
          </tr>
          
          <?php if($question['answers']) :?>
          
            <tr>
              <td height="20">&nbsp;</td>
            </tr>
          
            <tr>
              <td><b>Answers</b></td>
            </tr>

            <?php foreach($question['answers'] as $answer) :?>

              <tr>
                <td>
                  <table bgcolor="#f1f1f1" width="100%">
                    <tr>
                      <td class="answers">
                        <?php echo $answer['qa_title']?><br/>
                        <?php echo $answer['qa_description']?><br/>
                        Asked by <?php echo $answer['user_name']?> @ <?php echo format_time($answer['qa_created_at']);?>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td height="5">&nbsp;</td>
              </tr>
              

            <?php endforeach;?>
              
          <?php endif;?>
          
          <?php if($question['history']) :?>
          
            <tr>
              <td height="20">&nbsp;</td>
            </tr>
          
            <tr>
              <td><b>History</b></td>
            </tr>
            
            <tr>
              <td class="history">
                <ul>
                  <?php foreach($question['history'] as $history) :?>

                    <li><?php echo format_time($history['created_at']);?>: <?php echo $history['message']?></li>

                  <?php endforeach;?>
                </ul>
              </td>
            </tr>
              
          <?php endif;?>
            
        </table>
        <br/>

      <?php endforeach;?>
      
    </td>
  </tr>
  
</table>