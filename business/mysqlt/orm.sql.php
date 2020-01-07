<?php

$sql["insert"] = "
	INSERT INTO --table-- 
		--column_list--
	VALUES
    	--value_list--
";

$sql["get"] = "
	SELECT 
		--select_list-- 
	FROM
		--table--
	--join--
	--where--
	--order_by--
";

$sql["delete"] = "
	DELETE FROM 
		--table--
		--where--
";

$sql["update"] = "
	UPDATE 
		--table--
	SET 
		--value_list--
	--where--
";