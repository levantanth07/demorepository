<?phpclass HomeNewsForm extends Form{	function __construct()	{		Form::Form('HomeNewsForm');	}	function draw()	{		$this->map = array();		$cond = 'news.publish=1 and news.status <> "HIDE" and '.IDStructure::child_cond(DB::structure_id('category',3)).'';		$this->map['items'] = HomeNewsDB::get_news($cond);		$this->map['thuonghieu'] = HomeNewsDB::get_thuonghieu();		$this->map['description'] = DB::fetch('select description_1 from category where id = 3','description_1');    $this->parse_layout('list',$this->map);	}}?>