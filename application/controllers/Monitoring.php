<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Monitoring extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		if ($this->session->userdata("logged")<>1) {
	      redirect(site_url('login'));
	    }

	    $this->load->model('ModelMonitoring','mm');
	    $this->load->model('ModelAset','ma');

	    $this->load->library('upload');
	}

	public function index()
	{
		$data = array(
			'title' => 'Monitoring Aset',
			'active_menu_mt' => 'active',
			'mt' => $this->mm->getMonitoring()  
		);
	
		$this->load->view('layouts/header',$data);
		$this->load->view('monitoring/v_monitoring',$data);
		$this->load->view('layouts/footer');
	}

	public function tambahMonitoring()
	{
		$data = array(
			'title' => 'Monitoring Aset',
			'active_menu_mt' => 'active',
			'aset' => $this->ma->getAsetWujud()  
		);
	
		$this->load->view('layouts/header',$data);
		$this->load->view('monitoring/c_monitoring',$data);
		$this->load->view('layouts/footer');
	}

	public function simpanMonitoring()
	{
		// Pastikan folder upload ada
		if (!is_dir('src/img/aset/')) {
			mkdir('src/img/aset/', 0755, true);
		}

		$config['upload_path'] = './src/img/aset/';
        $config['allowed_types'] = '*'; // Allow all types temporarily
        $config['encrypt_name'] = TRUE;
        $config['max_size'] = 5120; // 5MB
        $config['overwrite'] = FALSE;
        $config['remove_spaces'] = TRUE;

        $this->upload->initialize($config);
        if(!empty($_FILES['foto']['name'])){

        	// Manual validation untuk file extension
        	$file_name = $_FILES['foto']['name'];
        	$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        	$allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp');

        	if(!in_array($file_ext, $allowed_ext)) {
        		$this->session->set_flashdata('gagal', 'Tipe file tidak diizinkan. Gunakan: JPG, JPEG, PNG, GIF, BMP, WEBP');
				redirect('monitoring/tambah');
				return;
        	}

            if ($this->upload->do_upload('foto')){
                $gbr = $this->upload->data();
                //Compress Image
                $config['image_library']='gd2';
                $config['source_image']='./src/img/aset/'.$gbr['file_name'];
                $config['create_thumb']= FALSE;
                $config['maintain_ratio']= FALSE;
                $config['quality']= '60%';
                $config['width']= 500;
                $config['height']= 500;
                $config['new_image']= './src/img/aset/'.$gbr['file_name'];
                $this->load->library('image_lib', $config);
                $this->image_lib->resize();

                $data = array(
                	'id_aset' => $this->input->post('id_aset'),
                	'kerusakan' => $this->input->post('kerusakan'),
                	'akibat' => $this->input->post('akibat'),
                	'faktor' => $this->input->post('faktor'),
                	'monitoring' => '', // Field monitoring dikosongkan
                	'pemeliharaan' => $this->input->post('pemeliharaan'),
                	'jml_rusak' => $this->input->post('jml_rusak'),
                	'foto' => $gbr['file_name'],
                	'updated_at' => date('Y-m-d H:i:s')
                );
                $result = $this->mm->storeMonitoring($data);

                if($result) {
	                // Update kondisi dan jenis bantuan aset
	                $update_data = array(
	                	'kondisi' => $this->input->post('kondisi'),
	                	'jenis_bantuan' => $this->input->post('jenis_bantuan')
	                );
	                $this->mm->updateKondisiAset($this->input->post('id_aset'), $update_data);

	               	$this->session->set_flashdata('sukses', 'Disimpan');
					redirect('monitoring');
                } else {
                	$this->session->set_flashdata('gagal', 'Gagal menyimpan data');
					redirect('monitoring/tambah');
                }
            } else {
            	$error = $this->upload->display_errors();
            	echo "Upload Error: " . $error . "<br>";
            	echo "File Info: <br>";
            	echo "Name: " . $_FILES['foto']['name'] . "<br>";
            	echo "Type: " . $_FILES['foto']['type'] . "<br>";
            	echo "Size: " . $_FILES['foto']['size'] . "<br>";
            	echo "Error: " . $_FILES['foto']['error'] . "<br>";
            	echo "Temp: " . $_FILES['foto']['tmp_name'] . "<br>";
            	echo "<a href='" . base_url('monitoring/tambah') . "'>Kembali</a>";
            	return;
            	// $this->session->set_flashdata('gagal', 'Gagal upload foto: ' . $error);
				// redirect('monitoring/tambah');
            }

        }else{
            $this->session->set_flashdata('gagal', 'Foto harus diupload');
			redirect('monitoring/tambah');
        }
	}

	public function detailMonitoring($id_monitoring)
	{
		$id_monitoring = $this->uri->segment(3);

		$data = array(
			'title' => 'Monitoring Aset',
			'active_menu_mt' => 'active',
			'mt' => $this->mm->getDetailMonitoring($id_monitoring),
			'aset' => $this->mm->getDetailAset($id_monitoring)  
		);
	
		$this->load->view('layouts/header',$data);
		$this->load->view('monitoring/d_monitoring',$data);
		$this->load->view('layouts/footer');
	}

	public function editMonitoring($id_monitoring)
	{
		$id_monitoring = $this->uri->segment(3);

		$data = array(
			'title' => 'Monitoring Aset',
			'active_menu_mt' => 'active',
			'aset' => $this->ma->getAsetWujud(),
			'mt' => $this->mm->getDetailMonitoring($id_monitoring)  
		);
	
		$this->load->view('layouts/header',$data);
		$this->load->view('monitoring/u_monitoring',$data);
		$this->load->view('layouts/footer');
	}

	public function ubahMonitoring()
	{
		$id_monitoring = $this->input->post('id_monitoring');
		if ($_FILES['foto']['name']){

			$config['upload_path'] = './src/img/aset/';
			$config['allowed_types'] = '*'; // Allow all types temporarily
			$config['encrypt_name'] = TRUE;
			$config['max_size'] = 5120; // 5MB

			// Manual validation untuk file extension
			$file_name = $_FILES['foto']['name'];
			$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
			$allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp');

			if(!in_array($file_ext, $allowed_ext)) {
				$this->session->set_flashdata('gagal', 'Tipe file tidak diizinkan. Gunakan: JPG, JPEG, PNG, GIF, BMP, WEBP');
				redirect('monitoring/edit/'.$id_monitoring);
				return;
			}

			$this->upload->initialize($config);
			if ( ! $this->upload->do_upload('foto')){
				$error = $this->upload->display_errors();
				$this->session->set_flashdata('gagal', 'Gagal upload: ' . $error);
				redirect('monitoring/edit/'.$id_monitoring);
			}else{
				$ambildata = $this->mm->getDetailAset($id_monitoring);
				foreach($ambildata as $foto){
					unlink('src/img/aset/'.$foto['foto']);
				}

				$gbr = $this->upload->data();
                //Compress Image
                $config['image_library']='gd2';
                $config['source_image']='src/img/aset/'.$gbr['file_name'];
                $config['create_thumb']= FALSE;
                $config['maintain_ratio']= FALSE;
                $config['quality']= '60%';
                $config['width']= 500;
                $config['height']= 500;
                $config['new_image']= 'src/img/aset/'.$gbr['file_name'];
                $this->load->library('image_lib', $config);
                $this->image_lib->resize();

                $data = array(
                	'id_aset' => $this->input->post('id_aset'),
                	'kerusakan' => $this->input->post('kerusakan'),
                	'akibat' => $this->input->post('akibat'),
                	'faktor' => $this->input->post('faktor'),
                	'monitoring' => '', // Field monitoring dikosongkan
                	'pemeliharaan' => $this->input->post('pemeliharaan'),
                	'jml_rusak' => $this->input->post('jml_rusak'),
                	'foto' => $gbr['file_name'],
                	'updated_at' => date('Y-m-d H:i:s')
                );
                unset($data['id_monitoring']);
                $this->mm->updateMonitoring($id_monitoring,$data);

                // Update kondisi dan jenis bantuan aset
                $update_data = array(
                	'kondisi' => $this->input->post('kondisi'),
                	'jenis_bantuan' => $this->input->post('jenis_bantuan')
                );
                $this->mm->updateKondisiAset($this->input->post('id_aset'), $update_data);

               	$this->session->set_flashdata('sukses', 'Diubah');
				redirect('monitoring');
			}
		}else{
			$data = array(
				'id_aset' => $this->input->post('id_aset'),
				'kerusakan' => $this->input->post('kerusakan'),
				'akibat' => $this->input->post('akibat'),
				'faktor' => $this->input->post('faktor'),
				'monitoring' => '', // Field monitoring dikosongkan
				'pemeliharaan' => $this->input->post('pemeliharaan'),
				'jml_rusak' => $this->input->post('jml_rusak'),
				'updated_at' => date('Y-m-d H:i:s')
			);
			unset($data['id_monitoring']);
			$res = $this->mm->updateMonitoring($id_monitoring,$data);

			// Update kondisi dan jenis bantuan aset
			$update_data = array(
				'kondisi' => $this->input->post('kondisi'),
				'jenis_bantuan' => $this->input->post('jenis_bantuan')
			);
			$this->mm->updateKondisiAset($this->input->post('id_aset'), $update_data);

			if($res>=1){
				$this->session->set_flashdata('sukses', 'Diubah');
				redirect('monitoring');
			}else{
				$this->session->set_flashdata('gagal', 'Diupload');
				redirect('monitoring/edit/'.$id_monitoring);
			}
		}
	}

	public function hapusMonitoring($id_monitoring)
	{
		$id_monitoring = $this->uri->segment(3);
        $this->db->where('id_monitoring',$id_monitoring);
        $get_image_file=$this->db->get('monitoring_aset')->row();
        @unlink('src/img/aset/'.$get_image_file->foto);
        $this->db->where('id_monitoring',$id_monitoring);
        $this->db->delete('monitoring_aset');
        $this->session->set_flashdata('sukses', 'Dihapus');
		redirect('monitoring');
	}

}

/* End of file Monitoring.php */
/* Location: ./application/controllers/Monitoring.php */