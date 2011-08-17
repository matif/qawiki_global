
<?php if($type == "signUp"):?>
  <p>
    Thank you for signing up on Q&Awiki Please checkout your email and start using the QAWiki services.  
  </p>
<?php else:?>
  <p>
    Thank you for conforming your mailing address. Please checkout your email and start using the QAWiki services.
    <a href="<?php echo base_url()?>dashboard">Q&Awiki Home</a>
  </p>
<?php endif;?>
