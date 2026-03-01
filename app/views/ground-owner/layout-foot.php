<?php foreach (($additionalJS ?? []) as $js): ?>
<script src="<?= htmlspecialchars($js) ?>"></script>
<?php endforeach; ?>
</body>
</html>
