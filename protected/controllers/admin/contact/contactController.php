<?php

/**
 * Created by PhpStorm.
 * User: shihao
 * Date: 17-11-12
 * Time: 下午4:05
 */
class ContactController extends Controller
{
    public function actionContactDetail($contactId)
    {
        $contact = CompanyLoginUser::model()->findByPk($contactId);
        $companyName = Company::model()->findByPk($contact->company_id)->name;
        $this->smarty->assign('contact', $contact);
        $this->smarty->assign('companyName',$companyName);
        $this->smarty->display('admin/contact/contactDetail.html');
    }

    public function actionToCompanyContactAdd($companyId)
    {
        $company = Company::model()->findByPk($companyId);
        $this->smarty->assign('companyName',$company->name);
        $this->smarty->assign('companyId',$companyId);
        $this->smarty->display('admin/company/contactAdd.html');
    }

    public function actionToContactEdit($contactId)
    {
        $contact = CompanyLoginUser::model()->findByPk($contactId);
        $companyName = Company::model()->findByPk($contact->company_id)->name;
        $this->smarty->assign('contact', $contact);
        $this->smarty->assign('companyName',$companyName);
        $this->smarty->display('admin/contact/contactEdit.html');
    }

    public function actionCompanyContact($companyId)
    {
        $cri = new CDbCriteria();
        $cri->condition="company_id=:companyId";
        $cri->params=array(":companyId"=>$companyId);
        $contactList = CompanyLoginUser::model()->findAll($cri);
        $this->smarty->assign('companyId',$companyId);
        $this->smarty->assign('contactList',$contactList);
        $this->smarty->display('admin/company/contactMain.html');
    }

    public function actionContactEdit(){
        $contactId=$_GET['contact-id'];
        $contact = CompanyLoginUser::model()->findByPk($contactId);
        $contact->name = $_GET['contact-name'];
        $contact->phone = $_GET['contact-mobile'];
        $contact->email = $_GET['contact-email'];
        $contact->admin_id = 0;
        if(isset($_GET['contact-sex'])){
            $contact->sex_id=$_GET['contact-sex'];
        }
        if (isset($_GET['contact-position'])) {
            $contact->duty = $_GET['contact-position'];
        }
        if (isset($_GET['contact-telephone'])) {
            $contact->telephone = $_GET['contact-telephone'];
        }
        if (isset($_GET['contact-classmate'])) {
            $contact->is_schoolfellow = $_GET['contact-classmate'];
        }
        if ($contact->update()) {
            echo "<script>alert('编辑成功');history.go(-2)</script>";
        } else {
            echo "<script>alert('编辑失败');history.go(-2)</script>";
        }

    }

    public function actionToList(){
        $companyList = Company::model()->findAll();
        $this->smarty->assign('name',"");
        $this->smarty->assign('sex',-1);
        $this->smarty->assign('telephone',"");
        $this->smarty->assign('company',0);
        $this->smarty->assign('email',"");
        $this->smarty->assign('sort',"");
        $this->smarty->assign('classmate',-1);
        $this->smarty->assign('phone',"");
        $this->smarty->assign('recordCount',0);
        $this->smarty->assign('companyList', $companyList);
        $this->smarty->display('admin/contact/contactMain.html');
    }

    public function actionContactMain()
    {
        if (isset($_GET['name'])) {
            $name = $_GET['name'];
        } else {
            $name = "";
        }
        if (isset($_GET['sex'])) {
            $sex_id = $_GET['sex'];
        } else {
            $sex_id = 0;
        }
        if (isset($_GET['telephone'])) {
            $phone = $_GET['telephone'];
        } else {
            $phone = "";
        }
        if (isset($_GET['company'])) {
            $companyId = $_GET['company'];
        } else {
            $companyId = 0;
        }
        if (isset($_GET['email'])) {
            $email = $_GET['email'];
        } else {
            $email = "";
        }
        if (isset($_GET['sort'])) {
            $type = $_GET['sort'];
        } else {
            $type = "";
        }
        if (isset($_GET['classmate'])&&$_GET['classmate']!=-1) {
            $is_schoolfellwo = $_GET['classmate'];
        } else {
            $is_schoolfellwo = 65535;
        }
        if (isset($_GET['phone'])) {
            $telephone = $_GET['phone'];
        } else {
            $telephone = "";
        }

        $cri = new CDbCriteria();
        $cri->with = array('companyName');
        $cri->select = ['company_id', 'name'];
        $conditions = "1=1 ";
        $params = array();
        if ($name != "") {
            $conditions .= " and t.name LIKE :name ";
            $params[':name'] = '%' . $name . '%';
        }
        if ($sex_id != 0 && $sex_id != -1) {
            $conditions .= " and sex_id =:sex_id";
            $params[':sex_id'] = $sex_id;
        }
        if ($phone != "") {
            $conditions .= " and t.phone =:phone ";
            $params[':phone'] = $phone;
        }
        if ($companyId != 0 && $companyId != "") {
            $conditions .= " and company_id=:companyId ";
            $params[':companyId'] = $companyId;
        }
        if ($email != "") {
            $conditions .= " and t.email LIKE :email";
            $params[':email'] = '%' . $email . '%';
        }
        if ($type != "") {
            $conditions .= " and t.type_id LIKE :type";
            $params[':type'] = '%' . $type . '%';
        }
        if ($is_schoolfellwo != -1 && $is_schoolfellwo != 65535) {
            $conditions .= " and is_schoolfellow =:is_schoolfellow";
            $params[':is_schoolfellow'] = $is_schoolfellwo;
        }
        if ($telephone != "") {
            $conditions .= " and telephone LIKE :telephone";
            $params[':telephone'] = '%' . $telephone . '%';
        }
        $cri->condition = $conditions;
        $cri->params = $params;
        $cri->group = 't.id';
        $cri->order = 't.id DESC';
        $cri->distinct = true;
        $data = CompanyLoginUser::model()->findAll($cri);
        $recordCount = CompanyLoginUser::model()->count($cri);
        $companyList = Company::model()->findAll();
        $this->smarty->assign('name',$name);
        $this->smarty->assign('sex',$sex_id);
        $this->smarty->assign('telephone',$phone);
        $this->smarty->assign('company',$companyId);
        $this->smarty->assign('email',$email);
        $this->smarty->assign('sort',$type);
        $this->smarty->assign('classmate',$is_schoolfellwo);
        $this->smarty->assign('phone',$telephone);
        $this->smarty->assign('recordCount',$recordCount);
        $this->smarty->assign('companyList', $companyList);
        $this->smarty->display('admin/contact/contactMain.html');
//        print_r($name);
    }

    public function actionContactList()
    {
        //条件获取
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        } else {
            $page = 0;
        }
        if (isset($_GET['name'])) {
            $name = $_GET['name'];
        } else {
            $name = "";
        }
        if (isset($_GET['sex'])) {
            $sex_id = $_GET['sex'];
        } else {
            $sex_id = 0;
        }
        if (isset($_GET['telephone'])) {
            $phone = $_GET['telephone'];
        } else {
            $phone = "";
        }
        if (isset($_GET['company'])) {
            $companyId = $_GET['company'];
        } else {
            $companyId = 0;
        }
        if (isset($_GET['email'])) {
            $email = $_GET['email'];
        } else {
            $email = "";
        }
        if (isset($_GET['sort'])) {
            $type = $_GET['sort'];
        } else {
            $type = "";
        }
        if (isset($_GET['classmate'])&&$_GET['classmate']!=-1) {
            $is_schoolfellwo = $_GET['classmate'];
        } else {
            $is_schoolfellwo = 65535;
        }
        if (isset($_GET['phone'])) {
            $telephone = $_GET['phone'];
        } else {
            $telephone = "";
        }


        $cri = new CDbCriteria();
        $cri->limit = 10;
        $cri->offset = ($page - 1) * 10;
        $cri->with = array('companyName');
        $cri->select = ['company_id', 'name'];
        $conditions = "1=1 ";
        $params = array();
        if ($name != "") {
            $conditions .= " and t.name LIKE :name ";
            $params[':name'] = '%' . $name . '%';
        }
        if ($sex_id != 0 && $sex_id != -1) {
            $conditions .= " and sex_id =:sex_id";
            $params[':sex_id'] = $sex_id;
        }
        if ($phone != "") {
            $conditions .= " and t.phone =:phone ";
            $params[':phone'] = $phone;
        }
        if ($companyId != 0 && $companyId != "") {
            $conditions .= " and company_id=:companyId ";
            $params[':companyId'] = $companyId;
        }
        if ($email != "") {
            $conditions .= " and t.email LIKE :email";
            $params[':email'] = '%' . $email . '%';
        }
        if ($type != "") {
            $conditions .= " and t.type_id LIKE :type";
            $params[':type'] = '%' . $type . '%';
        }
        if ($is_schoolfellwo != -1 && $is_schoolfellwo != 65535) {
            $conditions .= " and is_schoolfellow =:is_schoolfellow";
            $params[':is_schoolfellow'] = $is_schoolfellwo;
        }
        if ($telephone != "") {
            $conditions .= " and telephone LIKE :telephone";
            $params[':telephone'] = '%' . $telephone . '%';
        }
        $cri->condition = $conditions;
        $cri->params = $params;
        $cri->group = 't.id';
        $cri->order = 't.id DESC';
        $cri->distinct = true;
        $data = CompanyLoginUser::model()->findAll($cri);
        $recordCount = CompanyLoginUser::model()->count($cri);

        foreach ($data as $key => $value) {
            if (isset($value->companyName->name)) {
                $contactList[$key]['companyName'] = $value->companyName->name;
            }
            if (isset($value->id)) {
                $contactList[$key]['id'] = $value->id;
            }
            if (isset($value->name)) {
                $contactList[$key]['name'] = $value->name;
            }
            if (isset($value->phone)) {
                $contactList[$key]['phone'] = $value->phone;
            }
        }
        $json = CJSON::encode($contactList);
        if ($recordCount == 0) $json = CJSON::encode("0");
        $contactJson = '{"data":' . $json . ',"dataCount":"' . $recordCount . '"}';
        print   $contactJson;
    }

    public function actionToContactAdd()
    {
        $companyList = Company::model()->findAll();
        $this->smarty->assign('companyList', $companyList);
        $this->smarty->display('admin/contact/contactAdd.html');
    }

    public function actionContactAdd()
    {
        $contact = new CompanyLoginUser();
        $contact->name = $_GET['contact-name'];
        $contact->phone = $_GET['contact-mobile'];
        $contact->email = $_GET['contact-email'];
        $contact->company_id = $_GET['contact-company'];
        $contact->type_id = implode(" ", $_GET['contact-type']);
        $contact->admin_id = 0;
        if(isset($_GET['contact-sex'])){
            $contact->sex_id=$_GET['contact-sex'];
        }
        if (isset($_GET['contact-position'])) {
            $contact->duty = $_GET['contact-position'];
        }
        if (isset($_GET['contact-telephone'])) {
            $contact->telephone = $_GET['contact-telephone'];
        }
        if (isset($_GET['contact-classmate'])) {
            $contact->is_schoolfellow = $_GET['contact-classmate'];
        }
        if (strstr($contact->type_id, "信息发布联系人")) {
            $contact->password = md5($contact->phone);
        }
        if ($contact->save()) {
            echo "<script>alert('添加成功');history.go(-2)</script>";
        } else {
            echo "<script>alert('添加失败');history.go(-2)</script>";
        }
    }

    public function actionContactDel($contactId)
    {
        $contact = CompanyLoginUser::model()->findByPk($contactId);
        if ($contact->delete()) {
            echo "<script>alert('删除成功');history.go(-1)</script>";
        } else {
            echo "<script>alert('删除失败');history.go(-1)</script>";
        }
    }
}