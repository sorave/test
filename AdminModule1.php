<?php

class AdminModule extends CWebModule
{
	public function init()
	{
		
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'admin.models.*',
			'admin.components.*',
		));
		//指定默认的控制器，默认的方法
		$this->defaultController="test/fun1";
	}

	public function beforeControllerAction($controller, $action)
	{
		/*
		 * login控制器，不登录也能访问
		 * 除了login这个控制器外，都需要登录后才能访问  news
		 */
		//得到控制器的名称,controller中方的是对象
		$className=get_class($controller);
		$controllerName=str_replace('Controller','',$className);
		
		//对名称进行判断，如果不等于login，判断会话变量是否存在
		if(strtolower($controllerName)!='login'){
			if($_COOKIE['userid']){
				//判断有没有访问当前控制器及方法的权限
				//根据控制器名及方法名得到权值
				$powerOb=Power::model();
				$actionName="action".ucfirst($action->id);
				$pOb=$powerOb->find("controller=:c and action=:a",array(":c"=>$className,':a'=>$actionName));
				$powerId=$pOb->id;
				if(is_null($powerId)){
					return true;
				}
				//根据用户id，获取角色，获取角色权值数组
				$adminOb=Admin::model();
				$cOb=new CDbCriteria();
				$cOb->select="r.powers,a.id";
				$cOb->condition="a.id=".$_COOKIE['userid'];
				$cOb->alias="a";
				$cOb->join="inner join role as r on a.rid=r.id";
				$ob=$adminOb->find($cOb);
				$powerArr=unserialize($ob->powers);
				//判断
				if(in_array($powerId,$powerArr)){
					return true;
				}else{
					echo "<script type='text/javascript'>
					alert('亲，你没有权限。');
					location.replace('".$_SERVER['HTTP_REFERER']."');
					</script>";
					exit();
				}
				
			}else{
				//跳转
				header("location:index.php?r=admin/login/index");
				exit();
			}
		}else{//login
			return true;
		}
		
		
		
		
		
		
		
		
	}
}
