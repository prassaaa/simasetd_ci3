<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Laporan</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=base_url('home')?>">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Laporan</a></li>
            <li class="breadcrumb-item active">Data Aset</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <div class="flash-data-gagal" data-flashdatagagal="<?=$this->session->flashdata('gagal');?>"></div>

  <!-- Main content -->
  <section class="content">

    <!-- Default box -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          Cari Data Aset
        </h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
          <div class="card-body">
              <form action="<?=base_url('laporan/search_aset')?>" method="post">
                <div class="row mb-3">
                    <div class="col-3">
                        <label>Lokasi Aset</label>
                        <select name="id_lokasi" class="form-control" required>
                          <option value="">- Pilih Lokasi --</option>
                          <?php foreach ($lokasi as $row): ?>
                            <option value="<?=$row['id_lokasi'];?>" <?= (isset($filter['id_lokasi']) && $filter['id_lokasi'] == $row['id_lokasi']) ? 'selected' : '' ?>><?=$row['nama_lokasi'];?></option>
                          <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-3">
                        <label>Tahun Perolehan</label>
                        <select name="tahun_perolehan" class="form-control" required>
                          <option value="">- Pilih Tahun --</option>
                          <option value="semua" <?= (isset($filter['tahun_perolehan']) && $filter['tahun_perolehan'] == 'semua') ? 'selected' : '' ?>>Semua Tahun</option>
                          <?php
                          for($i = 2010 ; $i <= date('Y'); $i++){
                            $selected = (isset($filter['tahun_perolehan']) && $filter['tahun_perolehan'] == $i) ? 'selected' : '';
                            echo "<option value='$i' $selected>$i</option>";
                          }
                          ?>
                        </select>
                    </div>
                    <div class="col-3">
                        <label>Kategori</label>
                        <select name="id_kategori" class="form-control">
                          <option value="">- Semua Kategori --</option>
                          <?php foreach ($kategori as $row): ?>
                            <option value="<?=$row['id_kategori'];?>" <?= (isset($filter['id_kategori']) && $filter['id_kategori'] == $row['id_kategori']) ? 'selected' : '' ?>><?=$row['kode_kategori'];?> - <?=$row['nama_kategori'];?></option>
                          <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-3">
                        <label>Kondisi</label>
                        <select name="kondisi" class="form-control">
                          <option value="">- Semua Kondisi --</option>
                          <?php foreach ($kondisi as $row): ?>
                            <option value="<?=$row['kondisi'];?>" <?= (isset($filter['kondisi']) && $filter['kondisi'] == $row['kondisi']) ? 'selected' : '' ?>><?=$row['kondisi'];?></option>
                          <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                      <button type="submit" class="btn btn-block btn-outline-primary">Cari Data</button>
                    </div>
                    <div class="col">
                      <button type="reset" class="btn btn-block btn-outline-danger">Reset</button>
                    </div>
                </div>
              </form>
              <?php
              $print_url = base_url('laporan/print_aset_filter');
              $export_url = base_url('laporan/export_aset_filter');
              ?>
              <form action="<?=$print_url?>" method="post" style="display:inline;" target="_blank">
                <input type="hidden" name="id_lokasi" value="<?=$filter['id_lokasi']?>">
                <input type="hidden" name="tahun_perolehan" value="<?=$filter['tahun_perolehan']?>">
                <input type="hidden" name="id_kategori" value="<?=$filter['id_kategori']?>">
                <input type="hidden" name="kondisi" value="<?=$filter['kondisi']?>">
                <button type="submit" class="btn btn-danger mt-4">
                  <i class="fa fa-print" aria-hidden="true"></i> Print
                </button>
              </form>
              <form action="<?=$export_url?>" method="post" style="display:inline;">
                <input type="hidden" name="id_lokasi" value="<?=$filter['id_lokasi']?>">
                <input type="hidden" name="tahun_perolehan" value="<?=$filter['tahun_perolehan']?>">
                <input type="hidden" name="id_kategori" value="<?=$filter['id_kategori']?>">
                <input type="hidden" name="kondisi" value="<?=$filter['kondisi']?>">
                <button type="submit" class="btn btn-success mt-4">
                  <i class="fa fa-file" aria-hidden="true"></i> Export Excel
                </button>
              </form>
              <div class="mt-4">
                <div class="col">
                  <b>Lokasi Aset :</b> <?=$lok['nama_lokasi']?>
                </div>
              </div>
              <table class="table table-bordered mt-4">
                 <thead>
                   <tr>
                     <th>NO.</th>
                     <th>NAMA</th>
                     <th>VOLUME</th>
                     <th>SATUAN</th>
                     <th>HARGA (Rp.)</th>
                     <th>JUMLAH (Rp.)</th>
                   </tr>
                 </thead>
                 <tbody>
                  <?php 
                    $no=1;
                    $sum=0; 
                      foreach ($aset as $row): 
                    $sum+=$row['total_harga'];
                  ?>                 
                   <tr>
                     <td><?=$no++;?></td>
                     <td><?=$row['nama_barang']?></td>
                     <td><?=$row['volume']?></td>
                     <td><?=$row['satuan']?></td>
                     <td><?=laporan($row['harga'])?></td>
                     <td><?=laporan($row['total_harga'])?></td>
                   </tr>
                   <?php endforeach ?>
                   <tr>
                     <td colspan="5"><b>JUMLAH TOTAL</b></td>
                     <td><?=laporan($sum);?></td>
                   </tr>
                 </tbody>
               </table> 
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
            
          </div>
          <!-- /.card-footer-->
    </div>
    <!-- /.card -->

  </section>
  <!-- /.content -->
</div>
