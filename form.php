<div align=center>
    <form action=/ method=POST>
    <input type=text name=profile_link value="<?=htmlspecialchars($profile_link)?>" size=80><br><br>
    <?php if ($authenticated) { ?>
        <input type=submit name=action value=validate>
    <?php } else { ?>
        <input type=submit name=action value=login>
    <?php } ?>
    </form>
</div>
